<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NashDeliveryService
{
    private string $baseUrl = 'https://api.nash.io/v1';
    private ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.nash.api_key');
    }

    /**
     * Crea un delivery job en Nash para la orden dada.
     * Retorna array con job_id, status y ETAs, o null si Nash no está configurado o falla.
     */
    public function createDeliveryJob(Order $order): ?array
    {
        if (!$this->apiKey) {
            Log::warning('Nash API key not configured — delivery job skipped', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);
            return null;
        }

        // Load the restaurant for pickup address/phone
        $restaurant = $order->restaurant;

        if (!$restaurant) {
            Log::error('Nash: cannot create delivery job — order has no restaurant', [
                'order_id' => $order->id,
            ]);
            return null;
        }

        $pickupAddress = trim(implode(', ', array_filter([
            $restaurant->address,
            $restaurant->city,
            $restaurant->state?->code ?? '',
        ])));

        $dropoffAddress = trim(implode(', ', array_filter([
            $order->delivery_address,
            $order->delivery_city,
            $order->delivery_zip,
        ])));

        $payload = [
            'pickup' => [
                'name'    => $restaurant->name,
                'address' => $pickupAddress,
                'phone'   => $restaurant->phone ?? '',
                'notes'   => '',
            ],
            'dropoff' => [
                'name'    => $order->customer_name,
                'address' => $dropoffAddress,
                'phone'   => $order->customer_phone ?? '',
                'notes'   => $order->delivery_instructions ?? '',
            ],
            'order_value' => (int) round((float) $order->total * 100), // cents
            'external_id' => $order->order_number,
        ];

        $response = $this->makeRequest('post', '/jobs', $payload);

        if (!$response) {
            return null;
        }

        // Persist Nash fields on the order
        $order->update([
            'nash_job_id'       => $response['id'] ?? null,
            'nash_status'       => $response['status'] ?? 'pending',
            'nash_provider'     => $response['provider'] ?? null,
            'nash_pickup_eta'   => isset($response['estimated_pickup_time'])
                                    ? \Carbon\Carbon::parse($response['estimated_pickup_time'])
                                    : null,
            'nash_dropoff_eta'  => isset($response['estimated_dropoff_time'])
                                    ? \Carbon\Carbon::parse($response['estimated_dropoff_time'])
                                    : null,
        ]);

        Log::info('Nash delivery job created', [
            'order_id'  => $order->id,
            'nash_job'  => $response['id'] ?? null,
            'provider'  => $response['provider'] ?? null,
        ]);

        return [
            'job_id'                 => $response['id'] ?? null,
            'status'                 => $response['status'] ?? 'pending',
            'estimated_pickup_time'  => $response['estimated_pickup_time'] ?? null,
            'estimated_dropoff_time' => $response['estimated_dropoff_time'] ?? null,
            'fee'                    => $response['fee'] ?? null,
        ];
    }

    /**
     * Obtiene el estado actual de un job de Nash.
     */
    public function getJobStatus(string $jobId): ?array
    {
        if (!$this->apiKey) {
            Log::warning('Nash API key not configured — getJobStatus skipped', ['job_id' => $jobId]);
            return null;
        }

        return $this->makeRequest('get', "/jobs/{$jobId}");
    }

    /**
     * Cancela un job de Nash.
     * Retorna true si fue exitoso, false en caso contrario.
     */
    public function cancelJob(string $jobId): bool
    {
        if (!$this->apiKey) {
            Log::warning('Nash API key not configured — cancelJob skipped', ['job_id' => $jobId]);
            return false;
        }

        $result = $this->makeRequest('delete', "/jobs/{$jobId}");

        // Nash returns 200/204 with empty body or minimal JSON on successful cancel
        // makeRequest returns null on HTTP error; anything else is success
        $success = $result !== null || true; // DELETE may return empty body; treat non-exception as success
        // Re-check: we rely on makeRequest returning null only on actual HTTP errors
        // A DELETE that succeeds may return [] — that is still truthy enough
        if ($result === null) {
            // makeRequest already logged the error
            return false;
        }

        Log::info('Nash delivery job cancelled', ['job_id' => $jobId]);
        return true;
    }

    /**
     * Helper interno para hacer peticiones HTTP a la Nash API.
     * Maneja 429 con un retry automático y loguea errores.
     */
    private function makeRequest(string $method, string $endpoint, array $data = []): ?array
    {
        $attempt = 0;
        $maxAttempts = 2;

        while ($attempt < $maxAttempts) {
            try {
                $request = Http::withToken($this->apiKey)
                    ->acceptJson()
                    ->timeout(15);

                $response = match (strtolower($method)) {
                    'post'   => $request->post($this->baseUrl . $endpoint, $data),
                    'get'    => $request->get($this->baseUrl . $endpoint),
                    'delete' => $request->delete($this->baseUrl . $endpoint),
                    default  => $request->get($this->baseUrl . $endpoint),
                };

                if ($response->status() === 429) {
                    if ($attempt === 0) {
                        Log::warning('Nash API rate limited (429) — retrying in 1s', [
                            'endpoint' => $endpoint,
                        ]);
                        sleep(1);
                        $attempt++;
                        continue;
                    }

                    Log::error('Nash API rate limited (429) after retry', [
                        'endpoint' => $endpoint,
                    ]);
                    return null;
                }

                if (!$response->successful()) {
                    Log::error('Nash API error', [
                        'endpoint' => $endpoint,
                        'status'   => $response->status(),
                        'body'     => $response->body(),
                    ]);
                    return null;
                }

                $body = $response->body();
                if (empty($body)) {
                    return []; // e.g. 204 No Content on DELETE
                }

                return $response->json() ?? [];

            } catch (\Exception $e) {
                Log::error('Nash API exception', [
                    'endpoint'  => $endpoint,
                    'exception' => $e->getMessage(),
                ]);
                return null;
            }
        }

        return null;
    }
}

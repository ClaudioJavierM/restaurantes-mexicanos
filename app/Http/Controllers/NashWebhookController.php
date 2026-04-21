<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NashWebhookController extends Controller
{
    /**
     * Recibe webhooks de Nash y actualiza el estado de la orden.
     *
     * Eventos Nash:
     *   job.accepted   → out_for_delivery
     *   job.picked_up  → out_for_delivery
     *   job.delivered  → completed
     *   job.cancelled  → cancelled
     *   job.failed     → cancelled
     *
     * POST /nash/webhook
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        // ── Verificar firma (opcional) ──────────────────────────────
        $webhookSecret = config('services.nash.webhook_secret');

        if ($webhookSecret) {
            $signature = $request->header('X-Nash-Signature');

            if (!$signature) {
                Log::warning('Nash webhook: missing X-Nash-Signature header');
                return response()->json(['error' => 'Missing signature'], 401);
            }

            $expected = hash_hmac('sha256', $request->getContent(), $webhookSecret);

            if (!hash_equals($expected, $signature)) {
                Log::warning('Nash webhook: invalid signature', [
                    'received' => $signature,
                ]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }

        // ── Parsear payload ─────────────────────────────────────────
        $payload = $request->json()->all();

        $event  = $payload['event']  ?? $payload['type']  ?? null;
        $jobId  = $payload['job_id'] ?? $payload['id']    ?? ($payload['data']['id'] ?? null);
        $nashStatus = $payload['status'] ?? ($payload['data']['status'] ?? null);
        $provider   = $payload['provider'] ?? ($payload['data']['provider'] ?? null);

        if (!$event || !$jobId) {
            Log::warning('Nash webhook: missing event or job_id', ['payload' => $payload]);
            return response()->json(['error' => 'Missing event or job_id'], 422);
        }

        Log::info('Nash webhook received', [
            'event'       => $event,
            'job_id'      => $jobId,
            'nash_status' => $nashStatus,
        ]);

        // ── Buscar orden por nash_job_id ────────────────────────────
        $order = Order::where('nash_job_id', $jobId)->first();

        if (!$order) {
            Log::warning('Nash webhook: order not found for job', ['job_id' => $jobId]);
            // Return 200 so Nash doesn't retry indefinitely
            return response()->json(['message' => 'Order not found — ignored'], 200);
        }

        // ── Mapear evento Nash → estado Order ──────────────────────
        $newOrderStatus = match ($event) {
            'job.accepted',
            'job.picked_up'  => 'out_for_delivery',
            'job.delivered'  => 'completed',
            'job.cancelled',
            'job.failed'     => 'cancelled',
            default          => null,
        };

        // ── Actualizar nash_status siempre ──────────────────────────
        $nashFieldUpdates = [
            'nash_status' => $nashStatus ?? $event,
        ];

        if ($provider) {
            $nashFieldUpdates['nash_provider'] = $provider;
        }

        // ETAs pueden venir en actualizaciones intermedias
        if (!empty($payload['estimated_pickup_time'])) {
            $nashFieldUpdates['nash_pickup_eta'] = \Carbon\Carbon::parse($payload['estimated_pickup_time']);
        }
        if (!empty($payload['estimated_dropoff_time'])) {
            $nashFieldUpdates['nash_dropoff_eta'] = \Carbon\Carbon::parse($payload['estimated_dropoff_time']);
        }

        $order->update($nashFieldUpdates);

        // ── Actualizar estado de la orden si aplica ─────────────────
        if ($newOrderStatus && $order->status !== $newOrderStatus) {
            // Para cancelaciones, guardar motivo
            if ($newOrderStatus === 'cancelled') {
                $reason = match ($event) {
                    'job.cancelled' => 'Delivery cancelled by Nash',
                    'job.failed'    => 'Delivery failed via Nash',
                    default         => 'Delivery issue via Nash',
                };

                $order->update(['cancellation_reason' => $reason]);
            }

            $order->updateStatus($newOrderStatus);

            Log::info('Nash webhook: order status updated', [
                'order_id'   => $order->id,
                'order_no'   => $order->order_number,
                'old_status' => $order->getOriginal('status'),
                'new_status' => $newOrderStatus,
                'nash_event' => $event,
            ]);
        }

        return response()->json(['message' => 'Webhook processed'], 200);
    }
}

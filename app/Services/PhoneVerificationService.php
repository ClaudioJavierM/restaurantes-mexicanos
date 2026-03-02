<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PhoneVerificationService
{
    protected $twilioSid;
    protected $twilioToken;
    protected $twilioFrom;

    public function __construct()
    {
        $this->twilioSid = config('services.twilio.sid');
        $this->twilioToken = config('services.twilio.token');
        $this->twilioFrom = config('services.twilio.from');
    }

    /**
     * Envía un código de verificación por SMS
     *
     * @param string $phone Número de teléfono
     * @return array
     */
    public function sendVerificationCode(string $phone): array
    {
        // Si Twilio no está configurado, retornar como no disponible
        if (empty($this->twilioSid) || empty($this->twilioToken)) {
            return [
                'success' => false,
                'message' => 'SMS verification not configured',
                'mock' => true, // Para testing
            ];
        }

        // Limpiar el número de teléfono
        $cleanPhone = $this->cleanPhoneNumber($phone);

        // Generar código de 6 dígitos
        $code = random_int(100000, 999999);

        // Guardar en cache por 10 minutos
        Cache::put("phone_verification_{$cleanPhone}", $code, 600);

        try {
            // Enviar SMS vía Twilio
            $response = Http::withBasicAuth($this->twilioSid, $this->twilioToken)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->twilioSid}/Messages.json", [
                    'From' => $this->twilioFrom,
                    'To' => $cleanPhone,
                    'Body' => "Tu código de verificación es: {$code}\n\nRestaurantes Mexicanos Famosos",
                ]);

            if ($response->successful()) {
                Log::info("SMS sent to {$cleanPhone}");

                return [
                    'success' => true,
                    'message' => 'Verification code sent',
                    'expires_in' => 600, // seconds
                ];
            }

            Log::error('Twilio SMS failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send SMS',
                'error' => $response->body(),
            ];

        } catch (\Exception $e) {
            Log::error('Twilio exception: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'SMS service error',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verifica el código ingresado por el usuario
     *
     * @param string $phone
     * @param string $code
     * @return bool
     */
    public function verifyCode(string $phone, string $code): bool
    {
        $cleanPhone = $this->cleanPhoneNumber($phone);

        $storedCode = Cache::get("phone_verification_{$cleanPhone}");

        if ($storedCode && $storedCode == $code) {
            // Eliminar el código usado
            Cache::forget("phone_verification_{$cleanPhone}");

            // Guardar que este número fue verificado
            Cache::put("phone_verified_{$cleanPhone}", true, 86400); // 24 horas

            return true;
        }

        return false;
    }

    /**
     * Verifica si un número de teléfono es válido usando Twilio Lookup
     *
     * @param string $phone
     * @return array|null
     */
    public function lookupPhone(string $phone): ?array
    {
        if (empty($this->twilioSid) || empty($this->twilioToken)) {
            return null;
        }

        $cleanPhone = $this->cleanPhoneNumber($phone);

        try {
            $response = Http::withBasicAuth($this->twilioSid, $this->twilioToken)
                ->get("https://lookups.twilio.com/v1/PhoneNumbers/{$cleanPhone}", [
                    'Type' => 'carrier',
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'valid' => true,
                    'phone_number' => $data['phone_number'] ?? null,
                    'national_format' => $data['national_format'] ?? null,
                    'carrier' => $data['carrier'] ?? null,
                    'country_code' => $data['country_code'] ?? null,
                ];
            }

            return ['valid' => false];

        } catch (\Exception $e) {
            Log::error('Twilio Lookup error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Limpia el número de teléfono (solo dígitos con código de país)
     */
    protected function cleanPhoneNumber(string $phone): string
    {
        $cleaned = preg_replace('/\D/', '', $phone);

        // Si no tiene código de país, agregar +1 (USA)
        if (strlen($cleaned) === 10) {
            $cleaned = '1' . $cleaned;
        }

        return '+' . $cleaned;
    }

    /**
     * Verifica si un número ya fue verificado previamente
     */
    public function isPhoneVerified(string $phone): bool
    {
        $cleanPhone = $this->cleanPhoneNumber($phone);
        return Cache::has("phone_verified_{$cleanPhone}");
    }
}

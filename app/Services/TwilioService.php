<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    protected ?Client $client = null;
    protected string $from;
    protected string $whatsappFrom;
    protected ?string $messagingServiceSid = null;

    public function __construct()
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $this->from = config('services.twilio.from') ?? '';
        $this->whatsappFrom = config('services.twilio.whatsapp_from') ?? '';
        $this->messagingServiceSid = config('services.twilio.messaging_service_sid');

        if ($sid && $token) {
            $this->client = new Client($sid, $token);
        }
    }

    public function isConfigured(): bool
    {
        return $this->client !== null && !empty($this->from);
    }

    public function sendSms(string $to, string $message): bool
    {
        if (!$this->isConfigured()) {
            Log::warning('Twilio not configured. SMS not sent.');
            return false;
        }

        try {
            $this->client->messages->create(
                $this->formatPhoneNumber($to),
                [
                    'from' => $this->from,
                    'body' => $message
                ]
            );
            Log::info("SMS sent successfully to: $to");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send SMS: " . $e->getMessage());
            return false;
        }
    }

    public function sendWhatsApp(string $to, string $message): bool
    {
        if (!$this->client || empty($this->whatsappFrom)) {
            Log::warning('Twilio WhatsApp not configured.');
            return false;
        }

        try {
            $this->client->messages->create(
                'whatsapp:' . $this->formatPhoneNumber($to),
                [
                    'from' => $this->whatsappFrom,
                    'body' => $message
                ]
            );
            Log::info("WhatsApp message sent successfully to: $to");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send WhatsApp message: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send owner WhatsApp notification (wrapper around sendWhatsApp with logging).
     */
    public function sendOwnerWhatsApp(string $ownerPhone, string $message): bool
    {
        $result = $this->sendWhatsApp($ownerPhone, $message);
        if ($result) {
            Log::info("Owner WhatsApp notification sent to: $ownerPhone");
        }
        return $result;
    }

    public function sendNewOrderNotification($order, string $restaurantPhone): bool
    {
        $message = $this->buildOrderNotificationMessage($order);
        // Send via WhatsApp; fall back to SMS if WhatsApp is not configured
        if ($this->client && !empty($this->whatsappFrom)) {
            return $this->sendOwnerWhatsApp($restaurantPhone, $message);
        }
        return $this->sendSms($restaurantPhone, $message);
    }

    public function sendNewReservationNotification($reservation, string $ownerPhone): bool
    {
        $message = $this->buildReservationNotificationMessage($reservation);
        if ($this->client && !empty($this->whatsappFrom)) {
            return $this->sendOwnerWhatsApp($ownerPhone, $message);
        }
        return $this->sendSms($ownerPhone, $message);
    }

    protected function buildOrderNotificationMessage($order): string
    {
        $items = collect($order->items);
        $itemCount = $items->sum(fn($item) => $item['quantity'] ?? 1);

        $message  = "🍽️ NUEVO PEDIDO #{$order->order_number}\n";
        $message .= "Cliente: {$order->customer_name}\n";
        $message .= "Tel: {$order->customer_phone}\n";
        $message .= "Artículos: {$itemCount} items\n";
        $message .= "Total: \$" . number_format($order->total, 2);

        if ($order->pickup_time) {
            $message .= "\nRecoger: " . $order->pickup_time->format('d/m/Y h:i A');
        }

        if ($order->special_instructions) {
            $message .= "\nNotas: {$order->special_instructions}";
        }

        return $message;
    }

    protected function buildReservationNotificationMessage($reservation): string
    {
        $date = $reservation->reservation_date instanceof \Carbon\Carbon
            ? $reservation->reservation_date->format('d/m/Y')
            : \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y');

        $time = $reservation->reservation_time instanceof \Carbon\Carbon
            ? $reservation->reservation_time->format('h:i A')
            : \Carbon\Carbon::parse($reservation->reservation_time)->format('h:i A');

        $name = $reservation->user?->name ?? $reservation->guest_name ?? 'N/A';
        $phone = $reservation->user?->phone ?? $reservation->guest_phone ?? 'N/A';

        $message  = "📅 NUEVA RESERVACIÓN\n";
        $message .= "Cliente: {$name}\n";
        $message .= "Personas: {$reservation->party_size}\n";
        $message .= "Fecha: {$date}\n";
        $message .= "Hora: {$time}\n";

        if ($reservation->occasion && $reservation->occasion !== 'none') {
            $occasionLabel = match($reservation->occasion) {
                'birthday'    => 'Cumpleaños',
                'anniversary' => 'Aniversario',
                'date'        => 'Cita romántica',
                'business'    => 'Reunión de negocios',
                'celebration' => 'Celebración',
                'other'       => 'Otro',
                default       => $reservation->occasion,
            };
            $message .= "Ocasión: {$occasionLabel}\n";
        }

        $message .= "Tel: {$phone}";

        return $message;
    }

    /**
     * Send verification code via SMS using Messaging Service
     */
    public function sendVerificationCode(string $to, string $code): bool
    {
        if (!$this->client) {
            Log::warning('Twilio not configured. Verification SMS not sent.');
            return false;
        }

        try {
            $message = "Tu codigo de verificacion para Restaurantes Mexicanos es: {$code}. Expira en 15 minutos.";
            
            $params = [
                'body' => $message
            ];

            // Use Messaging Service if available (for A2P compliance)
            if ($this->messagingServiceSid) {
                $params['messagingServiceSid'] = $this->messagingServiceSid;
            } else {
                $params['from'] = $this->from;
            }

            $this->client->messages->create(
                $this->formatPhoneNumber($to),
                $params
            );

            Log::info("Verification code sent to: $to");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send verification code: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Initiate a voice call that speaks a verification code in Spanish.
     */
    public function makeVerificationCall(string $to, string $twimlToken): bool
    {
        if (!$this->client) {
            Log::warning('Twilio not configured. Verification call not made.');
            return false;
        }

        try {
            $twimlUrl = url('/webhooks/twilio/claim-twiml') . '?token=' . urlencode($twimlToken);

            $this->client->calls->create(
                $this->formatPhoneNumber($to),
                $this->from,
                [
                    'url' => $twimlUrl,
                    'method' => 'GET',
                    'timeout' => 30,
                ]
            );

            Log::info("Verification call initiated to: $to");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to initiate verification call: " . $e->getMessage());
            return false;
        }
    }

    protected function formatPhoneNumber(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($cleaned) === 10) {
            return '+1' . $cleaned;
        }

        if (strlen($cleaned) === 11 && substr($cleaned, 0, 1) === '1') {
            return '+' . $cleaned;
        }

        return '+' . $cleaned;
    }
}
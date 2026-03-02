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

    public function sendNewOrderNotification($order, string $restaurantPhone): bool
    {
        $message = $this->buildOrderNotificationMessage($order);
        return $this->sendSms($restaurantPhone, $message);
    }

    protected function buildOrderNotificationMessage($order): string
    {
        $itemsList = $order->items->map(function ($item) {
            return $item->quantity . 'x ' . $item->name;
        })->join(', ');

        $orderType = match($order->order_type) {
            'pickup' => 'Recoger en tienda',
            'delivery' => 'Envio a domicilio',
            'dine_in' => 'Comer en restaurante',
            default => $order->order_type
        };

        $message = "NUEVO PEDIDO #${order->order_number}\n";
        $message .= "Cliente: {$order->customer_name}\n";
        $message .= "Tel: {$order->customer_phone}\n";
        $message .= "Tipo: $orderType\n";
        $message .= "Items: $itemsList\n";
        $message .= "Total: \$" . number_format($order->total, 2);

        if ($order->order_type === 'delivery' && $order->delivery_address) {
            $message .= "\nDireccion: {$order->delivery_address}";
        }

        if ($order->scheduled_for) {
            $message .= "\nProgramado: " . $order->scheduled_for->format('h:i A');
        }

        if ($order->special_instructions) {
            $message .= "\nNotas: {$order->special_instructions}";
        }

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
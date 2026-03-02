<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReservationNotificationService
{
    /**
     * Send notification to restaurant when a new reservation is created
     */
    public function notifyRestaurantNewReservation(Reservation $reservation): void
    {
        $restaurant = $reservation->restaurant;

        if (!$restaurant->usesInternalReservations()) {
            return;
        }

        // Email notification (falls back to owner email if no specific notification email set)
        if ($restaurant->reservation_notify_email) {
            $this->sendEmailToRestaurant($reservation, 'new');
        }

        // SMS notification (Twilio)
        if ($restaurant->reservation_notify_sms && $restaurant->reservation_notification_phone) {
            $this->sendSmsToRestaurant($reservation, 'new');
        }

        // WhatsApp notification
        if ($restaurant->reservation_notify_whatsapp && $restaurant->reservation_notification_phone) {
            $this->sendWhatsAppToRestaurant($reservation, 'new');
        }
    }

    /**
     * Send pending notification to customer when reservation is first created
     */
    public function notifyCustomerPending(Reservation $reservation): void
    {
        $email = $reservation->getContactEmail();

        if ($email) {
            $this->sendEmailToCustomer($reservation, 'pending');
        }
    }

    /**
     * Send confirmation notification to customer
     */
    public function notifyCustomerConfirmation(Reservation $reservation): void
    {
        $restaurant = $reservation->restaurant;

        if (!$restaurant->reservation_send_confirmation) {
            return;
        }

        $email = $reservation->getContactEmail();
        $phone = $reservation->guest_phone;

        if ($email) {
            $this->sendEmailToCustomer($reservation, 'confirmed');
        }

        if ($phone) {
            $this->sendSmsToCustomer($reservation, 'confirmed');
        }
    }

    /**
     * Send reminder notification to customer
     */
    public function notifyCustomerReminder(Reservation $reservation): void
    {
        $restaurant = $reservation->restaurant;

        if (!$restaurant->reservation_send_reminder) {
            return;
        }

        $email = $reservation->getContactEmail();
        $phone = $reservation->guest_phone;

        if ($email) {
            $this->sendEmailToCustomer($reservation, 'reminder');
        }

        if ($phone) {
            $this->sendSmsToCustomer($reservation, 'reminder');
        }
    }

    /**
     * Send cancellation notification to customer
     */
    public function notifyCustomerCancellation(Reservation $reservation, ?string $reason = null): void
    {
        $email = $reservation->getContactEmail();
        $phone = $reservation->guest_phone;

        if ($email) {
            $this->sendEmailToCustomer($reservation, 'cancelled', ['reason' => $reason]);
        }

        if ($phone) {
            $this->sendSmsToCustomer($reservation, 'cancelled', ['reason' => $reason]);
        }
    }

    /**
     * Send email to restaurant
     */
    protected function sendEmailToRestaurant(Reservation $reservation, string $type): void
    {
        try {
            $restaurant = $reservation->restaurant;
            $to = $restaurant->reservation_notification_email;

            // Fall back to the restaurant owner's email if no specific notification email is set
            if (!$to && $restaurant->user_id) {
                $owner = \App\Models\User::find($restaurant->user_id);
                $to = $owner?->email;
            }

            if (!$to) {
                Log::warning('No email address to send reservation notification', [
                    'restaurant_id' => $restaurant->id,
                    'reservation_id' => $reservation->id,
                ]);
                return;
            }

            Mail::send("emails.reservation.restaurant-{$type}", [
                'reservation' => $reservation,
                'restaurant' => $restaurant,
            ], function ($message) use ($to, $restaurant, $type) {
                $subjects = [
                    'new' => "Nueva Reservación - {$restaurant->name}",
                    'cancelled' => "Reservación Cancelada - {$restaurant->name}",
                ];

                $message->to($to)
                    ->subject($subjects[$type] ?? "Actualización de Reservación - {$restaurant->name}");
            });

            Log::info("Email sent to restaurant", [
                'restaurant_id' => $restaurant->id,
                'reservation_id' => $reservation->id,
                'type' => $type,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send email to restaurant", [
                'error' => $e->getMessage(),
                'reservation_id' => $reservation->id,
            ]);
        }
    }

    /**
     * Send email to customer
     */
    protected function sendEmailToCustomer(Reservation $reservation, string $type, array $data = []): void
    {
        try {
            $email = $reservation->getContactEmail();
            if (!$email) {
                return;
            }

            $restaurant = $reservation->restaurant;

            Mail::send("emails.reservation.customer-{$type}", array_merge([
                'reservation' => $reservation,
                'restaurant' => $restaurant,
            ], $data), function ($message) use ($email, $restaurant, $type) {
                $subjects = [
                    'pending' => "Solicitud de Reservación Recibida - {$restaurant->name}",
                    'confirmed' => "Reservación Confirmada - {$restaurant->name}",
                    'reminder' => "Recordatorio de tu Reservación - {$restaurant->name}",
                    'cancelled' => "Reservación Cancelada - {$restaurant->name}",
                ];

                $message->to($email)
                    ->subject($subjects[$type] ?? "Actualización de tu Reservación - {$restaurant->name}");
            });

            Log::info("Email sent to customer", [
                'reservation_id' => $reservation->id,
                'type' => $type,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send email to customer", [
                'error' => $e->getMessage(),
                'reservation_id' => $reservation->id,
            ]);
        }
    }

    /**
     * Send SMS via Twilio to restaurant
     */
    protected function sendSmsToRestaurant(Reservation $reservation, string $type): void
    {
        try {
            $restaurant = $reservation->restaurant;
            $phone = $restaurant->reservation_notification_phone;

            if (!$phone || !config('services.twilio.sid')) {
                return;
            }

            $messages = [
                'new' => $this->getNewReservationSmsForRestaurant($reservation),
            ];

            $message = $messages[$type] ?? null;
            if (!$message) {
                return;
            }

            $this->sendTwilioSms($phone, $message);

            Log::info("SMS sent to restaurant", [
                'restaurant_id' => $restaurant->id,
                'reservation_id' => $reservation->id,
                'type' => $type,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send SMS to restaurant", [
                'error' => $e->getMessage(),
                'reservation_id' => $reservation->id,
            ]);
        }
    }

    /**
     * Send SMS via Twilio to customer
     */
    protected function sendSmsToCustomer(Reservation $reservation, string $type, array $data = []): void
    {
        try {
            $phone = $reservation->guest_phone;

            if (!$phone || !config('services.twilio.sid')) {
                return;
            }

            $messages = [
                'confirmed' => $this->getConfirmationSmsForCustomer($reservation),
                'reminder' => $this->getReminderSmsForCustomer($reservation),
                'cancelled' => $this->getCancellationSmsForCustomer($reservation, $data['reason'] ?? null),
            ];

            $message = $messages[$type] ?? null;
            if (!$message) {
                return;
            }

            $this->sendTwilioSms($phone, $message);

            Log::info("SMS sent to customer", [
                'reservation_id' => $reservation->id,
                'type' => $type,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send SMS to customer", [
                'error' => $e->getMessage(),
                'reservation_id' => $reservation->id,
            ]);
        }
    }

    /**
     * Send WhatsApp message to restaurant
     */
    protected function sendWhatsAppToRestaurant(Reservation $reservation, string $type): void
    {
        try {
            $restaurant = $reservation->restaurant;
            $phone = $restaurant->reservation_notification_phone;

            if (!$phone || !config('services.twilio.whatsapp_from')) {
                return;
            }

            $messages = [
                'new' => $this->getNewReservationWhatsAppForRestaurant($reservation),
            ];

            $message = $messages[$type] ?? null;
            if (!$message) {
                return;
            }

            $this->sendTwilioWhatsApp($phone, $message);

            Log::info("WhatsApp sent to restaurant", [
                'restaurant_id' => $restaurant->id,
                'reservation_id' => $reservation->id,
                'type' => $type,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send WhatsApp to restaurant", [
                'error' => $e->getMessage(),
                'reservation_id' => $reservation->id,
            ]);
        }
    }

    /**
     * Send SMS via Twilio
     */
    protected function sendTwilioSms(string $to, string $message): void
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.from');

        if (!$sid || !$token || !$from) {
            Log::warning("Twilio not configured for SMS");
            return;
        }

        $client = new \Twilio\Rest\Client($sid, $token);
        $client->messages->create($to, [
            'from' => $from,
            'body' => $message,
        ]);
    }

    /**
     * Send WhatsApp via Twilio
     */
    protected function sendTwilioWhatsApp(string $to, string $message): void
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.whatsapp_from');

        if (!$sid || !$token || !$from) {
            Log::warning("Twilio not configured for WhatsApp");
            return;
        }

        // Format phone for WhatsApp
        $to = preg_replace('/[^0-9+]/', '', $to);
        if (!str_starts_with($to, '+')) {
            $to = '+1' . $to;
        }

        $client = new \Twilio\Rest\Client($sid, $token);
        $client->messages->create("whatsapp:{$to}", [
            'from' => "whatsapp:{$from}",
            'body' => $message,
        ]);
    }

    // Message templates

    protected function getNewReservationSmsForRestaurant(Reservation $reservation): string
    {
        $date = $reservation->reservation_date->format('d/m/Y');
        $time = $reservation->reservation_time->format('g:i A');
        $name = $reservation->getContactName();

        return "Nueva reservación: {$name}, {$reservation->party_size} personas, {$date} a las {$time}. Código: {$reservation->confirmation_code}";
    }

    protected function getNewReservationWhatsAppForRestaurant(Reservation $reservation): string
    {
        $date = $reservation->reservation_date->format('d/m/Y');
        $time = $reservation->reservation_time->format('g:i A');
        $name = $reservation->getContactName();
        $phone = $reservation->guest_phone;
        $restaurant = $reservation->restaurant->name;

        return "🍽️ *Nueva Reservación*\n\n" .
            "📍 {$restaurant}\n" .
            "👤 {$name}\n" .
            "📞 {$phone}\n" .
            "👥 {$reservation->party_size} personas\n" .
            "📅 {$date}\n" .
            "🕐 {$time}\n" .
            "🔢 Código: *{$reservation->confirmation_code}*\n\n" .
            ($reservation->special_requests ? "📝 Notas: {$reservation->special_requests}" : "");
    }

    protected function getConfirmationSmsForCustomer(Reservation $reservation): string
    {
        $date = $reservation->reservation_date->format('d/m/Y');
        $time = $reservation->reservation_time->format('g:i A');
        $restaurant = $reservation->restaurant->name;

        return "Tu reservación en {$restaurant} para {$date} a las {$time} ha sido CONFIRMADA. Código: {$reservation->confirmation_code}";
    }

    protected function getReminderSmsForCustomer(Reservation $reservation): string
    {
        $date = $reservation->reservation_date->format('d/m/Y');
        $time = $reservation->reservation_time->format('g:i A');
        $restaurant = $reservation->restaurant->name;

        return "Recordatorio: Tu reservación en {$restaurant} es mañana {$date} a las {$time}. ¡Te esperamos!";
    }

    protected function getCancellationSmsForCustomer(Reservation $reservation, ?string $reason): string
    {
        $restaurant = $reservation->restaurant->name;
        $msg = "Tu reservación en {$restaurant} ha sido cancelada.";

        if ($reason) {
            $msg .= " Motivo: {$reason}";
        }

        return $msg;
    }
}

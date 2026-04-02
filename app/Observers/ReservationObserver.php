<?php

namespace App\Observers;

use App\Models\Reservation;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Log;

class ReservationObserver
{
    public function created(Reservation $reservation): void
    {
        try {
            $restaurant = $reservation->restaurant()->with('user')->first();

            if (!$restaurant || !$restaurant->user) {
                return;
            }

            $ownerPhone = $restaurant->user->phone;

            if (empty($ownerPhone)) {
                return;
            }

            $twilio = app(TwilioService::class);

            if (!$twilio->isConfigured()) {
                return;
            }

            $date = $reservation->reservation_date instanceof \Carbon\Carbon
                ? $reservation->reservation_date->format('d/m/Y')
                : \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y');

            $time = $reservation->reservation_time instanceof \Carbon\Carbon
                ? $reservation->reservation_time->format('h:i A')
                : \Carbon\Carbon::parse($reservation->reservation_time)->format('h:i A');

            $guestName  = $reservation->user?->name ?? $reservation->guest_name ?? 'N/A';
            $guestPhone = $reservation->user?->phone ?? $reservation->guest_phone ?? 'N/A';

            $occasionLine = '';
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
                $occasionLine = "\nOcasión: {$occasionLabel}";
            }

            $message  = "📅 NUEVA RESERVACIÓN en {$restaurant->name}\n";
            $message .= "Cliente: {$guestName}\n";
            $message .= "Personas: {$reservation->party_size}\n";
            $message .= "Fecha: {$date}\n";
            $message .= "Hora: {$time}";
            $message .= $occasionLine;
            $message .= "\nTel: {$guestPhone}";

            $twilio->sendOwnerWhatsApp($ownerPhone, $message);
        } catch (\Exception $e) {
            Log::error('ReservationObserver: failed to send owner WhatsApp notification. ' . $e->getMessage());
        }
    }
}

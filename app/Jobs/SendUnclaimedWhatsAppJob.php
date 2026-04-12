<?php

namespace App\Jobs;

use App\Models\Restaurant;
use App\Models\EmailSuppression;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class SendUnclaimedWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly int $restaurantId) {}

    public function handle(): void
    {
        try {
            $restaurant = Restaurant::with('state')->find($this->restaurantId);

            if (!$restaurant || $restaurant->user_id) return; // already claimed

            $phone = $this->normalizePhone($restaurant->phone ?? '');
            if (!$phone) {
                Log::info("SendUnclaimedWhatsAppJob: no valid phone for restaurant {$this->restaurantId}");
                return;
            }

            // Check suppression by phone
            if (EmailSuppression::isSuppressed($phone)) {
                Log::info("SendUnclaimedWhatsAppJob: {$phone} suppressed");
                return;
            }

            $claimUrl = url('/claim?restaurant=' . $restaurant->slug);
            $message = "Hola! 👋 *{$restaurant->name}* aparece en FAMER — Famous Mexican Restaurants y está recibiendo visitas.\n\n"
                . "¿Eres el dueño? Reclama tu perfil GRATIS en menos de 5 minutos y accede a estadísticas, reseñas y más:\n\n"
                . "→ {$claimUrl}\n\n"
                . "_Responde STOP para no recibir más mensajes._";

            $sid   = config('services.twilio.sid');
            $token = config('services.twilio.token');
            $from  = config('services.twilio.whatsapp_from', config('services.twilio.from', 'whatsapp:+14155238886'));

            if (!$sid || !$token) {
                Log::warning("SendUnclaimedWhatsAppJob: Twilio not configured");
                return;
            }

            $client = new Client($sid, $token);
            $client->messages->create(
                'whatsapp:' . $phone,
                ['from' => $from, 'body' => $message]
            );

            $restaurant->update(['whatsapp_outreach_sent_at' => now()]);
            Log::info("SendUnclaimedWhatsAppJob: WA sent to {$phone} for restaurant {$restaurant->id}");

        } catch (\Exception $e) {
            Log::error("SendUnclaimedWhatsAppJob: failed for restaurant {$this->restaurantId} — " . $e->getMessage());
        }
    }

    private function normalizePhone(string $phone): string
    {
        // Remove everything except digits and +
        $clean = preg_replace('/[^\d+]/', '', $phone);
        if (empty($clean)) return '';

        // Add +1 if US number without country code (10 digits)
        if (strlen($clean) === 10 && !str_starts_with($clean, '+')) {
            $clean = '+1' . $clean;
        }
        // Add + if starts with 1 and 11 digits
        if (strlen($clean) === 11 && str_starts_with($clean, '1')) {
            $clean = '+' . $clean;
        }

        // Must start with + to be valid international
        return str_starts_with($clean, '+') ? $clean : '';
    }
}

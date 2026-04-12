<?php
namespace App\Jobs;

use App\Models\Restaurant;
use App\Models\AnalyticsEvent;
use App\Models\RestaurantVote;
use App\Models\MenuItem;
use App\Mail\WeeklyStatsMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendWeeklyStatsEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly int $restaurantId) {}

    public function handle(): void
    {
        $restaurant = Restaurant::with('user', 'state')->find($this->restaurantId);
        if (!$restaurant) return;

        $email = $restaurant->owner_email ?? $restaurant->user?->email;
        $name  = $restaurant->owner_name  ?? $restaurant->user?->name ?? 'Propietario';
        if (!$email) return;

        $tier = $restaurant->subscription_tier ?? 'free';

        // ── Stats ──────────────────────────────────────────────
        $thisWeekViews = AnalyticsEvent::where('restaurant_id', $restaurant->id)
            ->where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
            ->where('created_at', '>=', now()->startOfWeek())
            ->count();

        $lastWeekViews = AnalyticsEvent::where('restaurant_id', $restaurant->id)
            ->where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
            ->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
            ->count();

        $monthlyViews = AnalyticsEvent::where('restaurant_id', $restaurant->id)
            ->where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        $monthlyVotes = RestaurantVote::where('restaurant_id', $restaurant->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $competitorCount = Restaurant::where('state_id', $restaurant->state_id)
            ->where('status', 'approved')
            ->where('id', '!=', $restaurant->id)
            ->count();

        // Week over week change
        $viewsChange = $lastWeekViews > 0
            ? round((($thisWeekViews - $lastWeekViews) / $lastWeekViews) * 100)
            : ($thisWeekViews > 0 ? 100 : 0);

        // ── Smart tip (only unused features, premium/elite) ────
        $tip = null;
        if (in_array($tier, ['premium', 'elite'])) {
            $tip = $this->getSmartTip($restaurant);
        }

        try {
            Mail::to($email)->send(new WeeklyStatsMail(
                restaurant: $restaurant,
                ownerName: $name,
                tier: $tier,
                thisWeekViews: $thisWeekViews,
                lastWeekViews: $lastWeekViews,
                viewsChange: $viewsChange,
                monthlyViews: $monthlyViews,
                monthlyVotes: $monthlyVotes,
                competitorCount: $competitorCount,
                tip: $tip,
            ));
            Log::info("Weekly stats email sent to {$email} for restaurant {$restaurant->id} (tier: {$tier})");
        } catch (\Exception $e) {
            Log::error("Failed to send weekly stats to {$email}: " . $e->getMessage());
        }
    }

    private function getSmartTip(Restaurant $restaurant): ?array
    {
        // Check features in order of impact. Return first unused one.
        $tips = [];

        // 1. Menu
        $menuCount = MenuItem::where('restaurant_id', $restaurant->id)->count();
        if ($menuCount === 0) {
            $tips[] = [
                'icon'   => '🍽️',
                'title'  => 'Agrega tu menú digital',
                'body'   => 'Los restaurantes con menú digital reciben 3x más clics. Agrega tus platillos en menos de 5 minutos.',
                'cta'    => 'Agregar menú →',
                'url'    => url('/owner/menu'),
            ];
        }

        // 2. Photos
        $photos = $restaurant->photos;
        $photoCount = is_array($photos) ? count($photos) : (is_string($photos) ? count(json_decode($photos, true) ?? []) : 0);
        if ($photoCount < 3) {
            $tips[] = [
                'icon'   => '📸',
                'title'  => 'Sube fotos de tu restaurante',
                'body'   => 'Los perfiles con 3+ fotos generan 5x más reservaciones. Una buena foto vale más que mil palabras.',
                'cta'    => 'Subir fotos →',
                'url'    => url('/owner/photos'),
            ];
        }

        // 3. Reservations
        if (!$restaurant->accepts_reservations) {
            $tips[] = [
                'icon'   => '📅',
                'title'  => 'Activa reservaciones online',
                'body'   => 'Acepta reservas directamente desde tu perfil FAMER. Reduce no-shows con recordatorios automáticos.',
                'cta'    => 'Activar reservaciones →',
                'url'    => url('/owner/reservations'),
            ];
        }

        // 4. Chatbot
        $chatbotSettings = $restaurant->chatbot_settings;
        $chatbotActive = !empty($chatbotSettings) && ($chatbotSettings['enabled'] ?? false);
        if (!$chatbotActive) {
            $tips[] = [
                'icon'   => '🤖',
                'title'  => 'Activa el Chatbot AI 24/7',
                'body'   => 'Responde preguntas de clientes automáticamente en español e inglés, incluso cuando estás ocupado.',
                'cta'    => 'Activar chatbot →',
                'url'    => url('/owner/chatbot'),
            ];
        }

        // 5. Fallback: share on social
        $tips[] = [
            'icon'   => '📲',
            'title'  => 'Comparte tu perfil en redes sociales',
            'body'   => 'Tu perfil FAMER tiene un link directo. Compártelo en Instagram, Facebook y WhatsApp para atraer más visitas.',
            'cta'    => 'Ver mi perfil →',
            'url'    => url('/restaurante/' . $restaurant->slug),
        ];

        // Return first unused tip (rotate weekly by restaurant ID to vary)
        $weekNumber = now()->weekOfYear;
        $index = ($restaurant->id + $weekNumber) % count($tips);
        return $tips[$index] ?? $tips[0];
    }
}

<?php

namespace App\Jobs;

use App\Mail\UnclaimedStatsMail;
use App\Models\AnalyticsEvent;
use App\Models\EmailLog;
use App\Models\EmailSuppression;
use App\Models\Restaurant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendUnclaimedStatsEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly int $restaurantId) {}

    public function handle(): void
    {
        try {
            $restaurant = Restaurant::with('state')->find($this->restaurantId);

            if (!$restaurant) {
                Log::warning("SendUnclaimedStatsEmailJob: restaurant {$this->restaurantId} not found.");
                return;
            }

            // Skip if already claimed
            if ($restaurant->is_claimed) {
                Log::info("SendUnclaimedStatsEmailJob: restaurant {$restaurant->id} is already claimed — skipping.");
                return;
            }

            // Resolve email
            $email = $restaurant->owner_email ?? $restaurant->email ?? null;

            if (!$email) {
                Log::info("SendUnclaimedStatsEmailJob: restaurant {$restaurant->id} has no email — skipping.");
                return;
            }

            // Check suppression
            if (EmailSuppression::isSuppressed($email)) {
                Log::info("SendUnclaimedStatsEmailJob: {$email} is suppressed — skipping restaurant {$restaurant->id}.");
                return;
            }

            // Query stats
            $monthlyViews = AnalyticsEvent::where('restaurant_id', $restaurant->id)
                ->where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
                ->where('created_at', '>=', now()->startOfMonth())
                ->count();

            $totalViews = AnalyticsEvent::where('restaurant_id', $restaurant->id)
                ->where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
                ->count();

            $competitorCount = Restaurant::where('state_id', $restaurant->state_id)
                ->where('status', 'approved')
                ->where('id', '!=', $restaurant->id)
                ->count();

            // Send email
            Mail::to($email)->send(new UnclaimedStatsMail(
                restaurant: $restaurant,
                monthlyViews: $monthlyViews,
                totalViews: $totalViews,
                competitorCount: $competitorCount,
            ));

            // Update tracking timestamp
            $restaurant->update(['unclaimed_stats_sent_at' => now()]);

            // Create EmailLog entry
            EmailLog::log([
                'type'          => EmailLog::TYPE_CAMPAIGN,
                'category'      => EmailLog::CATEGORY_FAMER,
                'to_email'      => $email,
                'from_email'    => 'hello@restaurantesmexicanosfamosos.com',
                'from_name'     => 'FAMER',
                'subject'       => $monthlyViews > 0
                    ? "{$monthlyViews} personas buscaron {$restaurant->name} este mes en FAMER"
                    : "Tu restaurante {$restaurant->name} está en FAMER — ¿eres el dueño?",
                'mailable_class' => UnclaimedStatsMail::class,
                'template'      => 'emails.unclaimed-stats',
                'restaurant_id' => $restaurant->id,
                'metadata'      => [
                    'monthly_views'    => $monthlyViews,
                    'total_views'      => $totalViews,
                    'competitor_count' => $competitorCount,
                ],
            ]);

            Log::info("SendUnclaimedStatsEmailJob: sent unclaimed stats to {$email} for restaurant {$restaurant->id} (monthly_views: {$monthlyViews}).");

        } catch (\Exception $e) {
            Log::error("SendUnclaimedStatsEmailJob: failed for restaurant {$this->restaurantId} — " . $e->getMessage());
        }
    }
}

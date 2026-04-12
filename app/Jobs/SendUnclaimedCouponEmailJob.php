<?php

namespace App\Jobs;

use App\Mail\UnclaimedCouponMail;
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

class SendUnclaimedCouponEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly int $restaurantId,
        public readonly string $couponCode = 'FAMER1MES',
    ) {}

    public function handle(): void
    {
        try {
            $restaurant = Restaurant::with('state')->find($this->restaurantId);

            if (!$restaurant) {
                Log::warning("SendUnclaimedCouponEmailJob: restaurant {$this->restaurantId} not found.");
                return;
            }

            // Skip if already claimed
            if ($restaurant->is_claimed) {
                Log::info("SendUnclaimedCouponEmailJob: restaurant {$restaurant->id} is already claimed — skipping.");
                return;
            }

            // Resolve email
            $email = $restaurant->owner_email ?? $restaurant->email ?? null;

            if (!$email) {
                Log::info("SendUnclaimedCouponEmailJob: restaurant {$restaurant->id} has no email — skipping.");
                return;
            }

            // Check suppression
            if (EmailSuppression::isSuppressed($email)) {
                Log::info("SendUnclaimedCouponEmailJob: {$email} is suppressed — skipping restaurant {$restaurant->id}.");
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
            Mail::to($email)->send(new UnclaimedCouponMail(
                restaurant: $restaurant,
                monthlyViews: $monthlyViews,
                competitorCount: $competitorCount,
                couponCode: $this->couponCode,
            ));

            // Update tracking timestamp
            $restaurant->update(['unclaimed_coupon_sent_at' => now()]);

            // Create EmailLog entry
            $subject = $monthlyViews > 0
                ? "Oferta especial — Premium por \$1 el primer mes para {$restaurant->name}"
                : "Oferta exclusiva para {$restaurant->name} — Premium por \$1";

            EmailLog::log([
                'type'           => EmailLog::TYPE_CAMPAIGN,
                'category'       => EmailLog::CATEGORY_FAMER,
                'to_email'       => $email,
                'from_email'     => 'hello@restaurantesmexicanosfamosos.com',
                'from_name'      => 'FAMER',
                'subject'        => $subject,
                'mailable_class' => UnclaimedCouponMail::class,
                'template'       => 'emails.unclaimed-coupon',
                'restaurant_id'  => $restaurant->id,
                'metadata'       => [
                    'monthly_views'    => $monthlyViews,
                    'total_views'      => $totalViews,
                    'competitor_count' => $competitorCount,
                    'coupon_code'      => $this->couponCode,
                ],
            ]);

            Log::info("SendUnclaimedCouponEmailJob: sent coupon email to {$email} for restaurant {$restaurant->id} (monthly_views: {$monthlyViews}, coupon: {$this->couponCode}).");

        } catch (\Exception $e) {
            Log::error("SendUnclaimedCouponEmailJob: failed for restaurant {$this->restaurantId} — " . $e->getMessage());
        }
    }
}

<?php

namespace App\Jobs;

use App\Models\Restaurant;
use App\Mail\FreeClaimFollowUpMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendFreeClaimFollowUpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly int $restaurantId
    ) {}

    public function handle(): void
    {
        $restaurant = Restaurant::find($this->restaurantId);

        if (!$restaurant) return;

        // Only send if still on free plan (didn't upgrade in 48h)
        if ($restaurant->subscription_tier !== 'free' && $restaurant->subscription_tier !== null) {
            return;
        }

        $email = $restaurant->owner_email ?? $restaurant->user?->email;
        $name  = $restaurant->owner_name  ?? $restaurant->user?->name ?? 'Propietario';

        if (!$email) return;

        // Get 48h visit stats
        $views48h = \App\Models\AnalyticsEvent::where('restaurant_id', $restaurant->id)
            ->where('event_type', \App\Models\AnalyticsEvent::EVENT_PAGE_VIEW)
            ->where('created_at', '>=', now()->subHours(48))
            ->count();

        $totalViews = \App\Models\AnalyticsEvent::where('restaurant_id', $restaurant->id)
            ->where('event_type', \App\Models\AnalyticsEvent::EVENT_PAGE_VIEW)
            ->count();

        $competitorCount = \App\Models\Restaurant::where('state_id', $restaurant->state_id)
            ->where('status', 'approved')
            ->where('id', '!=', $restaurant->id)
            ->count();

        try {
            Mail::to($email)->send(new FreeClaimFollowUpMail(
                $restaurant,
                $name,
                $views48h,
                $totalViews,
                $competitorCount
            ));
            Log::info("FreeClaimFollowUp sent to {$email} for restaurant #{$restaurant->id}");
        } catch (\Exception $e) {
            Log::warning("FreeClaimFollowUp failed for restaurant #{$restaurant->id}: " . $e->getMessage());
        }
    }
}

<?php
namespace App\Jobs;

use App\Models\Restaurant;
use App\Mail\TrialEndingReminderMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendTrialEndingReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly int $restaurantId) {}

    public function handle(): void
    {
        $restaurant = Restaurant::find($this->restaurantId);
        if (!$restaurant) return;

        // Only send if still on Elite trial
        if ($restaurant->subscription_tier !== 'elite') return;
        if (!$restaurant->trial_ends_at) return;

        $email = $restaurant->owner_email ?? $restaurant->user?->email;
        $name  = $restaurant->owner_name  ?? $restaurant->user?->name ?? 'Propietario';
        if (!$email) return;

        // Real stats for the trial period (last 30 days)
        $trialViews = \App\Models\AnalyticsEvent::where('restaurant_id', $restaurant->id)
            ->where('event_type', \App\Models\AnalyticsEvent::EVENT_PAGE_VIEW)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $totalViews = \App\Models\AnalyticsEvent::where('restaurant_id', $restaurant->id)
            ->where('event_type', \App\Models\AnalyticsEvent::EVENT_PAGE_VIEW)
            ->count();

        $competitorCount = \App\Models\Restaurant::where('state_id', $restaurant->state_id)
            ->where('status', 'approved')
            ->where('id', '!=', $restaurant->id)
            ->count();

        $daysLeft = (int) now()->diffInDays($restaurant->trial_ends_at, false);
        if ($daysLeft < 0) return; // trial already ended

        try {
            Mail::to($email)->send(new TrialEndingReminderMail(
                restaurant: $restaurant,
                ownerName: $name,
                trialViews: $trialViews,
                totalViews: $totalViews,
                competitorCount: $competitorCount,
                daysLeft: $daysLeft,
            ));
            Log::info("Trial ending reminder sent to {$email} for restaurant {$restaurant->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send trial ending reminder to {$email}: " . $e->getMessage());
        }
    }
}

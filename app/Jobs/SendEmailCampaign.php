<?php

namespace App\Jobs;

use App\Mail\CampaignMail;
use App\Models\EmailCampaign;
use App\Models\EmailLog;
use App\Models\Restaurant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour max
    public $tries = 1;

    public function __construct(
        public EmailCampaign $campaign
    ) {}

    public function handle(): void
    {
        $campaign = $this->campaign;

        // Mark as sending
        $campaign->start();

        Log::info("Starting email campaign: {$campaign->name} (ID: {$campaign->id})");

        try {
            // Get recipients based on audience filter
            $recipients = $this->getRecipients();

            // Update total recipients
            $campaign->update(['total_recipients' => $recipients->count()]);

            Log::info("Campaign {$campaign->id}: Found {$recipients->count()} recipients");

            // Process in chunks to avoid memory issues
            $recipients->chunk(100, function ($chunk) use ($campaign) {
                foreach ($chunk as $restaurant) {
                    // Check if campaign was paused
                    $campaign->refresh();
                    if ($campaign->status === EmailCampaign::STATUS_PAUSED) {
                        Log::info("Campaign {$campaign->id} paused, stopping...");
                        return false;
                    }

                    $this->sendToRestaurant($campaign, $restaurant);
                }
            });

            // Mark as complete
            $campaign->refresh();
            if ($campaign->status === EmailCampaign::STATUS_SENDING) {
                $campaign->complete();
                Log::info("Campaign {$campaign->id} completed successfully");
            }

        } catch (\Exception $e) {
            Log::error("Campaign {$campaign->id} failed: " . $e->getMessage());
            throw $e;
        }
    }

    protected function getRecipients()
    {
        $filter = $this->campaign->audience_filter ?? [];

        $query = Restaurant::query()
            ->whereNotNull('email')
            ->where('email', '!=', '');

        // Claimed status filter
        if (isset($filter['claimed_status']) && $filter['claimed_status'] !== 'all') {
            if ($filter['claimed_status'] === 'claimed') {
                $query->whereNotNull('user_id');
            } else {
                $query->whereNull('user_id');
            }
        }

        // Tier filter
        if (isset($filter['tier']) && $filter['tier'] !== 'all') {
            $query->where('tier', $filter['tier']);
        }

        // States filter
        if (!empty($filter['states'])) {
            $query->whereIn('state', $filter['states']);
        }

        // FAMER Score filter
        if (!empty($filter['min_famer_score']) || !empty($filter['max_famer_score'])) {
            $query->whereHas('famerScore', function ($q) use ($filter) {
                if (!empty($filter['min_famer_score'])) {
                    $q->where('overall_score', '>=', $filter['min_famer_score']);
                }
                if (!empty($filter['max_famer_score'])) {
                    $q->where('overall_score', '<=', $filter['max_famer_score']);
                }
            });
        }

        // Exclude unsubscribed emails
        // TODO: Add unsubscribe tracking

        return $query->select(['id', 'name', 'email', 'city', 'state', 'slug', 'user_id']);
    }

    protected function sendToRestaurant(EmailCampaign $campaign, Restaurant $restaurant): void
    {
        try {
            // Create email log
            $emailLog = EmailLog::forCampaign($campaign, [
                'to_email' => $restaurant->email,
                'to_name' => $restaurant->name,
                'restaurant_id' => $restaurant->id,
            ]);

            // Prepare merge data
            $mergeData = [
                'restaurant_name' => $restaurant->name,
                'owner_name' => $restaurant->user?->name ?? 'Propietario',
                'owner_email' => $restaurant->email,
                'restaurant_city' => $restaurant->city,
                'restaurant_state' => $restaurant->state,
                'famer_score' => $restaurant->famerScore?->overall_score ?? 'N/A',
                'famer_grade' => $restaurant->famerScore?->letter_grade ?? 'N/A',
                'claim_url' => url("/claim/{$restaurant->slug}"),
                'dashboard_url' => url("/dashboard"),
                'unsubscribe_url' => url("/unsubscribe?email=" . urlencode($restaurant->email) . "&token=" . md5($restaurant->email . config('app.key'))),
                'tracking_pixel' => $emailLog->getTrackingPixelUrl(),
            ];

            // Render content with merge tags
            $content = $campaign->renderContent($mergeData);

            // Add tracking pixel to content
            $content .= '<img src="' . $mergeData['tracking_pixel'] . '" width="1" height="1" style="display:none;" alt="" />';

            // Send email
            Mail::to($restaurant->email, $restaurant->name)
                ->send(new CampaignMail(
                    campaign: $campaign,
                    content: $content,
                    trackingToken: $emailLog->tracking_token
                ));

            // Update log
            $emailLog->update([
                'status' => EmailLog::STATUS_SENT,
                'sent_at' => now(),
            ]);

            // Update campaign stats
            $campaign->increment('sent_count');

            Log::debug("Campaign {$campaign->id}: Sent to {$restaurant->email}");

        } catch (\Exception $e) {
            Log::error("Campaign {$campaign->id}: Failed to send to {$restaurant->email}: " . $e->getMessage());

            if (isset($emailLog)) {
                $emailLog->update([
                    'status' => EmailLog::STATUS_FAILED,
                    'error_message' => $e->getMessage(),
                ]);
                $campaign->increment('failed_count');
            }
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Campaign job failed completely: " . $exception->getMessage());

        $this->campaign->update([
            'status' => EmailCampaign::STATUS_PAUSED,
            'notes' => ($this->campaign->notes ?? '') . "\n[ERROR] Job failed: " . $exception->getMessage(),
        ]);
    }
}

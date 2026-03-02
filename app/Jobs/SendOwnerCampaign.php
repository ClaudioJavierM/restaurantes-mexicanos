<?php

namespace App\Jobs;

use App\Models\OwnerCampaign;
use App\Models\OwnerCampaignSend;
use App\Models\RestaurantCustomer;
use App\Mail\OwnerCampaignMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOwnerCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 3600; // 1 hour

    public function __construct(
        public OwnerCampaign $campaign
    ) {}

    public function handle(): void
    {
        $campaign = $this->campaign;
        
        if (!$campaign->isSending()) {
            return;
        }

        $audience = $campaign->getAudience()->get();
        $sentCount = 0;

        foreach ($audience as $customer) {
            try {
                // Create send record
                $send = OwnerCampaignSend::create([
                    'campaign_id' => $campaign->id,
                    'customer_id' => $customer->id,
                ]);

                // Prepare content with merge tags
                $content = $this->replaceMergeTags($campaign->content, $customer, $campaign);

                // Send email
                Mail::to($customer->email)
                    ->send(new OwnerCampaignMail($campaign, $customer, $send, $content));

                $send->markSent();
                $sentCount++;
                $campaign->increment('sent_count');

            } catch (\Exception $e) {
                if (isset($send)) {
                    $send->markFailed($e->getMessage());
                }
                $campaign->increment('failed_count');
            }

            // Small delay to avoid rate limits
            usleep(100000); // 100ms
        }

        $campaign->markCompleted();
    }

    protected function replaceMergeTags(string $content, RestaurantCustomer $customer, OwnerCampaign $campaign): string
    {
        $replacements = [
            '{nombre}' => $customer->display_name,
            '{email}' => $customer->email,
            '{restaurante}' => $campaign->restaurant->name,
            '{puntos}' => number_format($customer->points),
            '{visitas}' => $customer->visits_count,
        ];

        // Add coupon if configured
        if ($campaign->coupon_config) {
            $replacements['{cupon}'] = $campaign->coupon_config['code'] ?? '';
            $replacements['{descuento}'] = $campaign->coupon_config['discount'] ?? '';
        }

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }
}

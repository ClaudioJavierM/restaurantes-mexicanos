<?php

namespace App\Jobs;

use App\Mail\CampaignMail;
use App\Models\EmailCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTestCampaignEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public EmailCampaign $campaign,
        public string $testEmail
    ) {}

    public function handle(): void
    {
        // Sample merge data for test
        $mergeData = [
            'restaurant_name' => 'Mi Restaurante de Prueba',
            'owner_name' => 'Juan Pérez',
            'owner_email' => $this->testEmail,
            'restaurant_city' => 'Los Angeles',
            'restaurant_state' => 'California',
            'famer_score' => '85',
            'famer_grade' => 'B+',
            'claim_url' => url('/claim/mi-restaurante-de-prueba'),
            'dashboard_url' => url('/dashboard'),
            'unsubscribe_url' => url('/unsubscribe?email=' . urlencode($this->testEmail)),
            'tracking_pixel' => url('/email/track/open/test-' . time()),
        ];

        $content = $this->campaign->renderContent($mergeData);

        Mail::to($this->testEmail)
            ->send(new CampaignMail(
                campaign: $this->campaign,
                content: $content,
                trackingToken: 'test-' . time(),
                isTest: true
            ));
    }
}

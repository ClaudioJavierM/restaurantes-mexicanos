<?php

namespace App\Mail;

use App\Models\EmailCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Headers;

class CampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public EmailCampaign $campaign,
        public string $content,
        public string $trackingToken,
        public bool $isTest = false
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->campaign->subject;
        if ($this->isTest) {
            $subject = "[PRUEBA] " . $subject;
        }

        return new Envelope(
            subject: $subject,
        );
    }

    public function headers(): Headers
    {
        return new Headers(
            text: [
                'X-Campaign-ID' => (string) $this->campaign->id,
                'X-Tracking-Token' => $this->trackingToken,
                'X-Mailer' => 'FAMER-Platform',
                'List-Unsubscribe' => '<mailto:unsubscribe@restaurantesmexicanosfamosos.com>',
                'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
                'Precedence' => 'bulk',
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.campaign',
            with: [
                'htmlContent' => $this->content,
                'previewText' => $this->campaign->preview_text,
                'campaignName' => $this->campaign->name,
                'isTest' => $this->isTest,
            ],
        );
    }
}

<?php

namespace App\Mail;

use App\Models\OwnerCampaign;
use App\Models\RestaurantCustomer;
use App\Models\OwnerCampaignSend;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class OwnerCampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public OwnerCampaign $campaign,
        public RestaurantCustomer $customer,
        public OwnerCampaignSend $send,
        public string $emailContent
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address'),
                $this->campaign->restaurant->name
            ),
            subject: $this->campaign->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.owner-campaign',
            with: [
                'content' => $this->emailContent,
                'restaurant' => $this->campaign->restaurant,
                'customer' => $this->customer,
                'trackingPixel' => $this->send->getTrackingPixelUrl(),
                'unsubscribeUrl' => $this->send->getUnsubscribeUrl(),
                'coupon' => $this->campaign->coupon_config,
            ]
        );
    }
}

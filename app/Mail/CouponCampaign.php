<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CouponCampaign extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $restaurantName;
    public string $contactName;
    public string $city;
    public string $state;
    public string $couponCode;
    public int $discountPercent;
    public string $expirationDate;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $restaurantName,
        string $contactName,
        string $city,
        string $state,
        string $couponCode = 'MFIMPORTS50',
        int $discountPercent = 50,
        ?string $expirationDate = null
    ) {
        $this->restaurantName = $restaurantName;
        $this->contactName = trim($contactName) ?: 'Propietario';
        $this->city = $city;
        $this->state = $state;
        $this->couponCode = $couponCode;
        $this->discountPercent = $discountPercent;
        $this->expirationDate = $expirationDate ?? now()->addDays(30)->format('d/m/Y');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🎁 {$this->restaurantName} - {$this->discountPercent}% de descuento EXCLUSIVO en Premium",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.coupon-campaign',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}

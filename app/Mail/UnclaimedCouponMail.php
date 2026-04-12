<?php

namespace App\Mail;

use App\Models\Restaurant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UnclaimedCouponMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Restaurant $restaurant,
        public readonly int $monthlyViews,
        public readonly int $competitorCount,
        public readonly string $couponCode = 'FAMER1MES',
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->monthlyViews > 0
            ? "Oferta especial — Premium por \$1 el primer mes para {$this->restaurant->name}"
            : "Oferta exclusiva para {$this->restaurant->name} — Premium por \$1";

        return new Envelope(
            from: new Address('hello@restaurantesmexicanosfamosos.com', 'FAMER'),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.unclaimed-coupon');
    }

    public function headers(): \Illuminate\Mail\Mailables\Headers
    {
        return new \Illuminate\Mail\Mailables\Headers(
            text: [
                'List-Unsubscribe'      => '<mailto:unsubscribe@restaurantesmexicanosfamosos.com>',
                'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
                'Precedence'            => 'bulk',
                'X-Mailer'              => 'FAMER-Platform',
            ]
        );
    }
}

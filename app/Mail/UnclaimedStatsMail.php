<?php

namespace App\Mail;

use App\Models\Restaurant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UnclaimedStatsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Restaurant $restaurant,
        public readonly int $monthlyViews,
        public readonly int $totalViews,
        public readonly int $competitorCount,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->monthlyViews > 0
            ? "{$this->monthlyViews} personas buscaron {$this->restaurant->name} este mes en FAMER"
            : "Tu restaurante {$this->restaurant->name} está en FAMER — ¿eres el dueño?";

        return new Envelope(
            from: new Address('hello@restaurantesmexicanosfamosos.com', 'FAMER'),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.unclaimed-stats');
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

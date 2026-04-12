<?php

namespace App\Mail;

use App\Models\Restaurant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AbandonedClaimMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Restaurant $restaurant,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('hello@restaurantesmexicanosfamosos.com', 'FAMER'),
            subject: "{$this->restaurant->name} — ¿terminaste de reclamar tu perfil?",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.abandoned-claim');
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

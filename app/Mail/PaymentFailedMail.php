<?php

namespace App\Mail;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class PaymentFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Restaurant $restaurant,
        public readonly User $user,
        public readonly string $updateUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('hello@restaurantesmexicanosfamosos.com', 'FAMER'),
            subject: "Acción requerida — pago rechazado para {$this->restaurant->name}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-failed');
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

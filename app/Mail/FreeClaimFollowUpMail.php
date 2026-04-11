<?php

namespace App\Mail;

use App\Models\Restaurant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FreeClaimFollowUpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Restaurant $restaurant,
        public readonly string $ownerName,
        public readonly int $views48h,
        public readonly int $totalViews,
        public readonly int $competitorCount
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->views48h > 0
            ? "{$this->views48h} personas visitaron tu restaurante — aquí está tu oportunidad"
            : "Tu restaurante está listo — aquí está el siguiente paso";

        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address('hello@restaurantesmexicanosfamosos.com', 'FAMER'),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.free-claim-followup',
        );
    }

    public function headers(): \Illuminate\Mail\Mailables\Headers
    {
        return new \Illuminate\Mail\Mailables\Headers(
            text: [
                'List-Unsubscribe' => '<mailto:unsubscribe@restaurantesmexicanosfamosos.com>',
                'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
                'Precedence' => 'bulk',
                'X-Mailer' => 'FAMER-Platform',
            ]
        );
    }
}

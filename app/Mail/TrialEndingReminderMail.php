<?php
namespace App\Mail;

use App\Models\Restaurant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class TrialEndingReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Restaurant $restaurant,
        public readonly string $ownerName,
        public readonly int $trialViews,
        public readonly int $totalViews,
        public readonly int $competitorCount,
        public readonly int $daysLeft,
    ) {}

    public function envelope(): Envelope
    {
        $views = $this->trialViews;
        $subject = $views > 0
            ? "{$views} personas vieron tu restaurante — tu prueba Elite termina en {$this->daysLeft} días"
            : "Tu prueba Elite termina en {$this->daysLeft} días — sigue disfrutando sin interrupciones";

        return new Envelope(
            from: new Address('hello@restaurantesmexicanosfamosos.com', 'FAMER'),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.trial-ending-reminder');
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

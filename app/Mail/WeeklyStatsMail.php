<?php
namespace App\Mail;

use App\Models\Restaurant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class WeeklyStatsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Restaurant $restaurant,
        public readonly string $ownerName,
        public readonly string $tier,
        public readonly int $thisWeekViews,
        public readonly int $lastWeekViews,
        public readonly int $viewsChange,
        public readonly int $monthlyViews,
        public readonly int $monthlyVotes,
        public readonly int $competitorCount,
        public readonly ?array $tip,
    ) {}

    public function envelope(): Envelope
    {
        $change = $this->viewsChange;
        $views  = $this->thisWeekViews;

        if ($views === 0) {
            $subject = "Reporte semanal — {$this->restaurant->name}";
        } elseif ($change > 0) {
            $subject = "↑{$change}% más visitas esta semana — {$this->restaurant->name}";
        } elseif ($change < 0) {
            $subject = "Tu perfil necesita atención — {$this->restaurant->name}";
        } else {
            $subject = "{$views} personas vieron tu restaurante esta semana";
        }

        return new Envelope(
            from: new Address('hello@restaurantesmexicanosfamosos.com', 'FAMER'),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $view = in_array($this->tier, ['premium', 'elite'])
            ? 'emails.weekly-stats-premium'
            : 'emails.weekly-stats-free';

        return new Content(view: $view);
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

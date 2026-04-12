<?php

namespace App\Mail;

use App\Models\EmailLog;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialEndingMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Restaurant $restaurant,
        public readonly User $user,
        public readonly int $daysLeft,
    ) {}

    public function envelope(): Envelope
    {
        $isUS = $this->restaurant->country === 'US';

        $subject = $isUS
            ? "Your Elite trial ends in {$this->daysLeft} days — keep your benefits"
            : "Tu prueba Elite termina en {$this->daysLeft} días — mantén tus beneficios";

        return new Envelope(
            from: new Address('hello@restaurantesmexicanosfamosos.com', 'FAMER'),
            to: [new Address($this->user->email, $this->user->name)],
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.trial-ending');
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

    /**
     * After the mail is sent, record an EmailLog entry for tracking.
     */
    public function sent(\Illuminate\Mail\SentMessage $message): void
    {
        EmailLog::create([
            'type'          => EmailLog::TYPE_TRANSACTIONAL,
            'category'      => 'trial_reminder',
            'to_email'      => $this->user->email,
            'to_name'       => $this->user->name,
            'from_email'    => 'hello@restaurantesmexicanosfamosos.com',
            'from_name'     => 'FAMER',
            'subject'       => $this->envelope()->subject,
            'mailable_class'=> static::class,
            'status'        => 'sent',
            'sent_at'       => now(),
            'restaurant_id' => $this->restaurant->id,
            'user_id'       => $this->user->id,
            'provider'      => 'resend',
        ]);
    }
}

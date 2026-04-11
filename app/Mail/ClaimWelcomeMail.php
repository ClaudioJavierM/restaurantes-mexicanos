<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Headers;
use App\Models\Restaurant;
use App\Models\EmailLog;
use Illuminate\Support\Facades\Mail;

class ClaimWelcomeMail extends Mailable
{
    public function __construct(
        public Restaurant $restaurant,
        public string $ownerName,
        public string $ownerEmail,
        public string $plan = 'free',
        public string $couponCode = 'FAMER30',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@restaurantesmexicanosfamosos.com', 'FAMER'),
            subject: "¡Bienvenido a FAMER, {$this->ownerName}! Tu restaurante está reclamado",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.claim-welcome');
    }

    public function headers(): Headers
    {
        return new Headers(
            text: [
                'List-Unsubscribe'      => '<mailto:unsubscribe@restaurantesmexicanosfamosos.com>',
                'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
                'Precedence'            => 'bulk',
                'X-Mailer'              => 'FAMER-Platform',
            ]
        );
    }

    /**
     * Send the welcome email and create an EmailLog record.
     * Usage: ClaimWelcomeMail::sendAndLog($restaurant, $ownerName, $ownerEmail, $plan);
     */
    public static function sendAndLog(
        Restaurant $restaurant,
        string $ownerName,
        string $ownerEmail,
        string $plan = 'free'
    ): void {
        $subject = "¡Bienvenido a FAMER, {$ownerName}! Tu restaurante está reclamado";
        $mailable = new self($restaurant, $ownerName, $ownerEmail, $plan);

        try {
            Mail::to($ownerEmail)->send($mailable);

            EmailLog::create([
                'type'           => EmailLog::TYPE_TRANSACTIONAL,
                'category'       => EmailLog::CATEGORY_CLAIM,
                'mailable_class' => self::class,
                'from_email'     => 'noreply@restaurantesmexicanosfamosos.com',
                'from_name'      => 'FAMER',
                'to_email'       => $ownerEmail,
                'to_name'        => $ownerName,
                'subject'        => $subject,
                'restaurant_id'  => $restaurant->id,
                'status'         => EmailLog::STATUS_SENT,
                'sent_at'        => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('ClaimWelcomeMail failed: ' . $e->getMessage(), [
                'restaurant_id' => $restaurant->id,
                'to_email'      => $ownerEmail,
            ]);
        }
    }
}

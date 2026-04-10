<?php

namespace App\Mail;

use App\Models\Restaurant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class ClaimInvitation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Restaurant $restaurant;
    public string $claimUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        $this->claimUrl = route('claim.restaurant') . '?search=' . urlencode($restaurant->name);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->restaurant->name} — Tu perfil ya está en el directorio de restaurantes mexicanos",
        );
    }

    public function headers(): Headers
    {
        return new Headers(
            text: [
                'X-Mailer' => 'FAMER-Platform',
                'List-Unsubscribe' => '<mailto:unsubscribe@restaurantesmexicanosfamosos.com>',
                'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
                'Precedence' => 'bulk',
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.claim-invitation',
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

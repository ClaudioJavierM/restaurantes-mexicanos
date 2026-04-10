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

class FamerHowItWorks extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Restaurant $restaurant;
    public string $claimUrl;
    public string $restaurantName;

    public function __construct(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        $this->restaurantName = $restaurant->name;
        $this->claimUrl = "https://famousmexicanrestaurants.com/restaurante/" . $restaurant->slug;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Tu restaurante aun puede participar en FAMER Awards 2026",
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

    public function content(): Content
    {
        return new Content(
            view: "emails.famer-how-it-works",
        );
    }
}

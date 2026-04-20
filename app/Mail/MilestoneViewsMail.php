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

class MilestoneViewsMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public bool $skipAutoLog = true;

    public Restaurant $restaurant;
    public int $viewCount;
    public int $milestone;
    public string $claimUrl;
    public string $premiumUrl;

    public function __construct(Restaurant $restaurant, int $viewCount, int $milestone = 50)
    {
        $this->restaurant = $restaurant;
        $this->viewCount  = $viewCount;
        $this->milestone  = $milestone;
        $this->claimUrl   = route('claim.restaurant') . '?search=' . urlencode($restaurant->name);
        $this->premiumUrl = route('claim.restaurant') . '?search=' . urlencode($restaurant->name) . '&plan=premium';
    }

    public function envelope(): Envelope
    {
        $subjects = [
            50  => "🎉 {$this->restaurant->name} ya tiene {$this->viewCount} visitas — sé el #1 de tu ciudad",
            100 => "🔥 {$this->viewCount} visitas y subiendo — {$this->restaurant->name} está ganando terreno",
            150 => "⭐ {$this->restaurant->name} está entre los más visitados — no dejes pasar esta oportunidad",
        ];
        return new Envelope(subject: $subjects[$this->milestone] ?? $subjects[50]);
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
            view: 'emails.milestone-views',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

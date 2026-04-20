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
    public string $status; // unclaimed | claimed | premium | elite
    public string $claimUrl;
    public string $premiumUrl;
    public string $eliteUrl;
    public string $dashboardUrl;

    public function __construct(Restaurant $restaurant, int $viewCount, int $milestone = 50, string $status = 'unclaimed')
    {
        $this->restaurant   = $restaurant;
        $this->viewCount    = $viewCount;
        $this->milestone    = $milestone;
        $this->status       = $status;
        $this->claimUrl     = route('claim.restaurant') . '?search=' . urlencode($restaurant->name);
        $this->premiumUrl   = route('claim.restaurant') . '?search=' . urlencode($restaurant->name) . '&plan=premium';
        $this->eliteUrl     = route('claim.restaurant') . '?search=' . urlencode($restaurant->name) . '&plan=elite';
        $this->dashboardUrl = route('owner.dashboard') ?? config('app.url') . '/owner/dashboard';
    }

    public function envelope(): Envelope
    {
        $subjects = [
            'unclaimed' => [
                50  => "🎉 {$this->restaurant->name} ya tiene {$this->viewCount} visitas — sé el #1 de tu ciudad",
                100 => "🔥 {$this->viewCount} visitas y subiendo — {$this->restaurant->name} está ganando terreno",
                150 => "⭐ {$this->restaurant->name} está entre los más visitados — no dejes pasar esto",
            ],
            'claimed' => [
                50  => "📈 {$this->viewCount} visitas — el siguiente paso para dominar {$this->restaurant->city}",
                100 => "🔥 {$this->viewCount} visitas con perfil activo — es hora de destacarte en {$this->restaurant->city}",
                150 => "⭐ {$this->viewCount} visitas — {$this->restaurant->name} está listo para el #1",
            ],
            'premium' => [
                50  => "🚀 {$this->viewCount} visitas con Premium — lleva {$this->restaurant->name} al máximo nivel",
                100 => "🏆 {$this->viewCount} visitas — ¿listo para dominar {$this->restaurant->city} con Elite?",
                150 => "👑 {$this->viewCount} visitas — {$this->restaurant->name} merece el plan Elite",
            ],
            'elite' => [
                50  => "👑 {$this->viewCount} visitas — {$this->restaurant->name} sigue siendo el #1",
                100 => "🏆 {$this->viewCount} visitas — mantén tu liderazgo en {$this->restaurant->city}",
                150 => "⭐ {$this->viewCount} visitas — {$this->restaurant->name} domina {$this->restaurant->city}",
            ],
        ];

        $subject = $subjects[$this->status][$this->milestone]
            ?? "🎉 {$this->restaurant->name} alcanzó {$this->viewCount} visitas en FAMER";

        return new Envelope(subject: $subject);
    }

    public function headers(): Headers
    {
        return new Headers(
            text: [
                'X-Mailer'               => 'FAMER-Platform',
                'List-Unsubscribe'       => '<mailto:unsubscribe@restaurantesmexicanosfamosos.com>',
                'List-Unsubscribe-Post'  => 'List-Unsubscribe=One-Click',
                'Precedence'             => 'bulk',
            ],
        );
    }

    public function content(): Content
    {
        $views = [
            'unclaimed' => 'emails.milestone-views',
            'claimed'   => 'emails.milestone-claimed',
            'premium'   => 'emails.milestone-premium',
            'elite'     => 'emails.milestone-elite',
        ];

        return new Content(view: $views[$this->status] ?? 'emails.milestone-views');
    }

    public function attachments(): array
    {
        return [];
    }
}

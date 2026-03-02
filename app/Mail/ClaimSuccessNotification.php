<?php

namespace App\Mail;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClaimSuccessNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Restaurant $restaurant;
    public User $user;
    public string $plan;
    public string $tempPassword;
    public string $dashboardUrl;
    public string $resetPasswordUrl;

    public function __construct(Restaurant $restaurant, User $user, string $plan, string $tempPassword)
    {
        $this->restaurant = $restaurant;
        $this->user = $user;
        $this->plan = $plan;
        $this->tempPassword = $tempPassword;
        $this->dashboardUrl = url('/owner/' . $restaurant->slug);
        $this->resetPasswordUrl = url('/forgot-password');
    }

    public function envelope(): Envelope
    {
        $planName = match($this->plan) {
            'elite' => 'Elite',
            'premium' => 'Premium',
            default => ucfirst($this->plan),
        };

        return new Envelope(
            subject: "Bienvenido a {$planName}! Tu restaurante {$this->restaurant->name} esta listo",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.claim-success',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

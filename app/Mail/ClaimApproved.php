<?php

namespace App\Mail;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClaimApproved extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Restaurant $restaurant;
    public User $user;
    public string $dashboardUrl;
    public string $resetPasswordUrl;

    public function __construct(Restaurant $restaurant, User $user)
    {
        $this->restaurant = $restaurant;
        $this->user = $user;
        $this->dashboardUrl = route('owner.dashboard', ['restaurant' => $restaurant->id]);
        $this->resetPasswordUrl = route('password.request');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Felicidades! Tu restaurante {$this->restaurant->name} ha sido verificado",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.claim-approved',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

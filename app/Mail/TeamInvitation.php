<?php

namespace App\Mail;

use App\Models\RestaurantTeamMember;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TeamInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public RestaurantTeamMember $member
    ) {}

    public function envelope(): Envelope
    {
        $restaurant = $this->member->restaurant->name;

        return new Envelope(
            subject: "Invitacion para unirte al equipo de {$restaurant}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.team-invitation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

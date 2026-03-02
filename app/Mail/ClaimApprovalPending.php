<?php

namespace App\Mail;

use App\Models\ClaimVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClaimApprovalPending extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public ClaimVerification $claim;
    public int $reminderNumber;

    public function __construct(ClaimVerification $claim, int $reminderNumber = 1)
    {
        $this->claim = $claim;
        $this->reminderNumber = $reminderNumber;
    }

    public function envelope(): Envelope
    {
        $restaurant = $this->claim->restaurant;
        $subject = $this->reminderNumber === 1 
            ? "Tu verificacion de {$restaurant->name} esta siendo procesada"
            : "Actualizacion: Tu restaurante {$restaurant->name} sera aprobado pronto";
            
        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.claim-approval-pending');
    }

    public function attachments(): array
    {
        return [];
    }
}

<?php

namespace App\Mail;

use App\Models\ClaimVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClaimVerificationReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public ClaimVerification $claim;
    public int $reminderNumber;
    public string $verifyUrl;

    public function __construct(ClaimVerification $claim, int $reminderNumber = 1)
    {
        $this->claim = $claim;
        $this->reminderNumber = $reminderNumber;
        $this->verifyUrl = route('claim.verify', ['verification' => $claim->id]);
    }

    public function envelope(): Envelope
    {
        $restaurant = $this->claim->restaurant;
        $subject = $this->reminderNumber === 1 
            ? "Recordatorio: Completa la verificacion de {$restaurant->name}"
            : "Ultimo recordatorio: Tu codigo de verificacion esta por expirar";
            
        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.claim-verification-reminder');
    }

    public function attachments(): array
    {
        return [];
    }
}

<?php

namespace App\Notifications;

use App\Models\ClaimVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClaimVerificationCode extends Notification
{
    use Queueable;

    protected $verification;

    public function __construct(ClaimVerification $verification)
    {
        $this->verification = $verification;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $restaurant = $this->verification->restaurant;
        $code = $this->verification->verification_code;
        $expiresAt = $this->verification->code_expires_at;

        return (new MailMessage)
            ->subject('Código de Verificación - ' . config('app.name'))
            ->greeting('¡Hola ' . $this->verification->owner_name . '!')
            ->line('Has solicitado reclamar el siguiente restaurante:')
            ->line('**' . $restaurant->name . '**')
            ->line($restaurant->address . ', ' . $restaurant->city)
            ->line('')
            ->line('Tu código de verificación es:')
            ->line('## **' . $code . '**')
            ->line('')
            ->line('Este código expira en 30 minutos (' . $expiresAt->format('h:i A') . ').')
            ->line('Si no solicitaste este código, puedes ignorar este email.')
            ->action('Verificar Ahora', route('claim.verify', $this->verification->id))
            ->line('Gracias por usar ' . config('app.name') . '!');
    }
}

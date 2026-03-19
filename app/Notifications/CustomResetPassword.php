<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends ResetPassword
{
    public function toMail($notifiable)
    {
        $url = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject('Restablecer tu contraseña - Restaurantes Mexicanos Famosos')
            ->view('emails.reset-password', [
                'url' => $url,
                'user' => $notifiable,
            ]);
    }
}

<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Recupera tu contraseña — FAMER')
            ->from('noreply@restaurantesmexicanosfamosos.com', 'FAMER — Restaurantes Mexicanos')
            ->view('emails.reset-password', [
                'url'      => $url,
                'name'     => $notifiable->name,
                'expireIn' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60),
            ]);
    }
}

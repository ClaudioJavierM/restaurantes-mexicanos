<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class BilingualVerifyEmail extends VerifyEmail
{
    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $locale = app()->getLocale();

        if ($locale === 'en') {
            return $this->buildEnglishMessage($verificationUrl, $notifiable);
        }

        return $this->buildSpanishMessage($verificationUrl, $notifiable);
    }

    /**
     * Build the English version of the email.
     */
    protected function buildEnglishMessage(string $url, $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verify Your Email Address - Famous Mexican Restaurants')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Welcome to Famous Mexican Restaurants! We\'re excited to have you join our community.')
            ->line('Please click the button below to verify your email address and activate your account.')
            ->action('Verify Email Address', $url)
            ->line('This verification link will expire in 60 minutes.')
            ->line('If you did not create an account, no further action is required.')
            ->salutation('Thank you for choosing Famous Mexican Restaurants!');
    }

    /**
     * Build the Spanish version of the email.
     */
    protected function buildSpanishMessage(string $url, $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verifica tu Dirección de Correo - Restaurantes Mexicanos Famosos')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('¡Bienvenido a Restaurantes Mexicanos Famosos! Nos emociona tenerte en nuestra comunidad.')
            ->line('Por favor haz clic en el botón de abajo para verificar tu dirección de correo y activar tu cuenta.')
            ->action('Verificar Correo Electrónico', $url)
            ->line('Este enlace de verificación expirará en 60 minutos.')
            ->line('Si no creaste una cuenta, no es necesario realizar ninguna acción.')
            ->salutation('¡Gracias por elegir Restaurantes Mexicanos Famosos!');
    }

    /**
     * Get the verification URL for the given notifiable.
     */
    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}

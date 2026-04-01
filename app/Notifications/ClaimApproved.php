<?php

namespace App\Notifications;

use App\Models\Restaurant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClaimApproved extends Notification
{
    use Queueable;

    protected $restaurant;
    protected $loginUrl;

    public function __construct(Restaurant $restaurant, string $loginUrl)
    {
        $this->restaurant = $restaurant;
        $this->loginUrl = $loginUrl;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('¡Reclamación Aprobada! - ' . config('app.name'))
            ->greeting('¡Felicidades!')
            ->line('Tu reclamación para **' . $this->restaurant->name . '** ha sido aprobada.')
            ->line('')
            ->line('**Lo que puedes hacer ahora:**')
            ->line('✓ Ver estadísticas y análisis de tu restaurante')
            ->line('✓ Responder a reseñas de clientes')
            ->line('✓ Actualizar información y fotos')
            ->line('✓ Subir hasta 10 fotos adicionales (Plan Gratuito)')
            ->line('✓ Crear ofertas y promociones')
            ->line('')
            ->line('**Plan Gratuito incluye:**')
            ->line('• Dashboard con análisis básicos')
            ->line('• Hasta 10 fotos')
            ->line('• Respuestas a reseñas')
            ->line('• Perfil verificado con insignia')
            ->line('')
            ->action('Acceder a Mi Panel', $this->loginUrl)
            ->line('')
            ->line('¿Quieres más? Actualiza a Premium ($39/mes) o Elite ($79/mes) para:')
            ->line('• Fotos ilimitadas')
            ->line('• Análisis avanzados')
            ->line('• Publicidad destacada')
            ->line('• Cupones y promociones')
            ->line('• Email marketing')
            ->line('')
            ->line('Gracias por unirte a ' . config('app.name') . '!');
    }
}

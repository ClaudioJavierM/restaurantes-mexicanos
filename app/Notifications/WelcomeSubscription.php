<?php

namespace App\Notifications;

use App\Models\Restaurant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeSubscription extends Notification
{
    use Queueable;

    protected $restaurant;
    protected $plan;
    protected $dashboardUrl;

    public function __construct(Restaurant $restaurant, string $plan = 'free')
    {
        $this->restaurant = $restaurant;
        $this->plan = $plan;
        $this->dashboardUrl = url('/owner/' . $restaurant->slug);
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return match($this->plan) {
            'elite' => $this->eliteEmail(),
            'premium' => $this->premiumEmail(),
            default => $this->freeEmail(),
        };
    }

    protected function freeEmail(): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 ¡Bienvenido! Tu restaurante ha sido verificado - ' . config('app.name'))
            ->greeting('¡Felicidades, ' . $this->restaurant->name . '!')
            ->line('Tu restaurante ha sido verificado exitosamente. Ahora tienes acceso a tu panel de propietario.')
            ->line('')
            ->line('**📋 Lo que incluye tu Plan Gratuito:**')
            ->line('')
            ->line('✅ **Perfil Verificado** - Badge verde de verificacion visible para clientes')
            ->line('✅ **Editar Informacion** - Actualiza horarios, telefono, direccion')
            ->line('✅ **Subir Fotos** - Hasta 5 fotos de tu restaurante')
            ->line('✅ **Responder Resenas** - Interactua con tus clientes')
            ->line('✅ **Ver Estadisticas Basicas** - Vistas y clics en tu perfil')
            ->line('✅ **Integracion con Google Maps** - Direcciones y navegacion')
            ->line('')
            ->line('**🔒 Funciones Premium (disponibles al actualizar):**')
            ->line('')
            ->line('• Menu Digital + Codigo QR')
            ->line('• Sistema de Reservaciones')
            ->line('• Pedidos para Recoger')
            ->line('• Dashboard de Analiticas Avanzadas')
            ->line('• Fotos y Videos Ilimitados')
            ->line('• Posicion Destacada en Busquedas')
            ->line('')
            ->action('Acceder a Mi Dashboard', $this->dashboardUrl)
            ->line('')
            ->line('**💡 Consejo:** Completa tu perfil al 100% para atraer mas clientes.')
            ->line('')
            ->salutation('¡Exito con tu negocio! - El equipo de ' . config('app.name'));
    }

    protected function premiumEmail(): MailMessage
    {
        return (new MailMessage)
            ->subject('⭐ ¡Bienvenido a Premium! - ' . config('app.name'))
            ->greeting('¡Felicidades, ' . $this->restaurant->name . '!')
            ->line('Tu suscripcion **Premium** esta activa. Ahora tienes acceso a todas las herramientas para hacer crecer tu negocio.')
            ->line('')
            ->line('**⭐ Todo lo que incluye tu Plan Premium:**')
            ->line('')
            ->line('✅ **Badge Premium Verificado** - Destacate de la competencia')
            ->line('✅ **Top 3 en Busquedas Locales** - Mayor visibilidad')
            ->line('✅ **Menu Digital + Codigo QR** - Clientes escanean y ven tu menu')
            ->line('✅ **Widget de Pedidos Online** - Recibe pedidos para recoger')
            ->line('✅ **Sistema de Reservaciones** - Gestiona reservas facilmente')
            ->line('✅ **Dashboard de Analiticas** - Metricas detalladas de tu negocio')
            ->line('✅ **Fotos y Videos Ilimitados** - Muestra todo tu restaurante')
            ->line('✅ **Chatbot AI 24/7** - Responde preguntas automaticamente')
            ->line('✅ **Programa de Lealtad** - Fideliza a tus clientes')
            ->line('')
            ->line('**🎁 BONUS: 4 Cupones Trimestrales**')
            ->line('Ahorra hasta $2,000/ano en equipos, ingredientes y servicios de nuestros proveedores asociados.')
            ->line('')
            ->action('Acceder a Mi Dashboard Premium', $this->dashboardUrl)
            ->line('')
            ->line('**💡 Primeros pasos recomendados:**')
            ->line('1. Sube tu menu digital')
            ->line('2. Activa el sistema de reservaciones')
            ->line('3. Configura el chatbot AI')
            ->line('')
            ->salutation('¡Mucho exito! - El equipo de ' . config('app.name'));
    }

    protected function eliteEmail(): MailMessage
    {
        return (new MailMessage)
            ->subject('🏆 ¡Bienvenido a Elite! La maxima distincion - ' . config('app.name'))
            ->greeting('¡Bienvenido al club Elite, ' . $this->restaurant->name . '!')
            ->line('Eres parte del grupo selecto de restaurantes con la **maxima distincion**. Tu suscripcion Elite incluye todo para dominar tu mercado.')
            ->line('')
            ->line('**🏆 Todo lo de Premium MAS:**')
            ->line('')
            ->line('✅ **Badge Elite Dorado** - La maxima distincion visible')
            ->line('✅ **Posicion #1 Garantizada** - Siempre primero en tu area')
            ->line('✅ **App Movil White Label** - Tu propia app con tu marca')
            ->line('✅ **Website Builder Completo** - Crea tu sitio web profesional')
            ->line('✅ **Asistente AI Avanzado** - Respuestas personalizadas')
            ->line('✅ **Marketing Automatizado** - Campanas de email y SMS')
            ->line('✅ **Fotografia Profesional** - Sesion trimestral incluida')
            ->line('✅ **Account Manager Dedicado** - Soporte prioritario')
            ->line('✅ **Integracion POS** - Conecta con tu sistema de ventas')
            ->line('✅ **Cobertura de Medios y PR** - Te promovemos activamente')
            ->line('')
            ->line('**🎁 BONUS: 6 Cupones con 15% Extra**')
            ->line('Ahorra hasta $4,500/ano con descuentos exclusivos de proveedores Elite.')
            ->line('')
            ->action('Acceder a Mi Dashboard Elite', $this->dashboardUrl)
            ->line('')
            ->line('**📞 Tu Account Manager te contactara en las proximas 24 horas** para ayudarte a configurar todo y maximizar tu inversion.')
            ->line('')
            ->line('**💡 Como Elite, tienes acceso prioritario a:**')
            ->line('• Soporte por WhatsApp directo')
            ->line('• Sesiones de estrategia mensuales')
            ->line('• Acceso anticipado a nuevas funciones')
            ->line('')
            ->salutation('¡Bienvenido a la elite! - El equipo de ' . config('app.name'));
    }
}

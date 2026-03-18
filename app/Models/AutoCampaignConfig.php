<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoCampaignConfig extends Model
{
    protected $fillable = [
        'restaurant_id', 'type', 'is_active', 'subject', 'message',
        'send_days_before', 'coupon_code', 'coupon_discount_percent',
        'coupon_valid_days', 'last_run_at', 'total_sent',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
    ];

    public static array $types = [
        'birthday'     => 'Cumpleaños',
        'reactivation' => 'Reactivación (clientes inactivos)',
        'welcome'      => 'Bienvenida (nuevo cliente)',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    // Default message templates
    public static function defaultMessage(string $type, string $restaurantName): string
    {
        return match($type) {
            'birthday' => "¡Feliz cumpleaños! En {$restaurantName} queremos celebrar contigo. " .
                          "Presenta este mensaje y obtén tu descuento especial de cumpleaños. ¡Te esperamos!",
            'reactivation' => "¡Te extrañamos en {$restaurantName}! " .
                              "Ha pasado un tiempo desde tu última visita. Vuelve y disfruta de una oferta exclusiva para ti.",
            'welcome' => "¡Bienvenido a la familia {$restaurantName}! " .
                         "Gracias por tu visita. Te enviamos un cupón de bienvenida para tu próxima vez.",
            default => "Tienes un mensaje especial de {$restaurantName}.",
        };
    }

    public static function defaultSubject(string $type, string $restaurantName): string
    {
        return match($type) {
            'birthday' => "🎂 ¡Feliz Cumpleaños de parte de {$restaurantName}!",
            'reactivation' => "¡Te extrañamos! Vuelve a {$restaurantName}",
            'welcome' => "¡Bienvenido a {$restaurantName}! 🎉",
            default => "Mensaje especial de {$restaurantName}",
        };
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmsAutomation extends Model
{
    protected $fillable = [
        'restaurant_id',
        'name',
        'trigger_type',
        'message_template',
        'delay_minutes',
        'conditions',
        'coupon_code',
        'coupon_discount',
        'coupon_type',
        'is_active',
        'sends_count',
        'clicks_count',
        'conversions_count',
        'revenue_generated',
    ];

    protected $casts = [
        'conditions' => 'array',
        'is_active' => 'boolean',
        'revenue_generated' => 'decimal:2',
    ];

    // Trigger Types
    const TRIGGER_ABANDONED_CART = 'abandoned_cart';
    const TRIGGER_WINBACK = 'winback';
    const TRIGGER_BIRTHDAY = 'birthday';
    const TRIGGER_LOYALTY_MILESTONE = 'loyalty_milestone';
    const TRIGGER_POST_ORDER = 'post_order';
    const TRIGGER_REVIEW_REQUEST = 'review_request';
    const TRIGGER_WELCOME = 'welcome';
    const TRIGGER_POINTS_EXPIRING = 'points_expiring';

    public static function triggerTypes(): array
    {
        return [
            self::TRIGGER_ABANDONED_CART => 'Carrito Abandonado',
            self::TRIGGER_WINBACK => 'Reactivación (Win-Back)',
            self::TRIGGER_BIRTHDAY => 'Cumpleaños',
            self::TRIGGER_LOYALTY_MILESTONE => 'Milestone de Puntos',
            self::TRIGGER_POST_ORDER => 'Post-Orden',
            self::TRIGGER_REVIEW_REQUEST => 'Solicitar Reseña',
            self::TRIGGER_WELCOME => 'Bienvenida',
            self::TRIGGER_POINTS_EXPIRING => 'Puntos por Expirar',
        ];
    }

    public static function defaultTemplates(): array
    {
        return [
            self::TRIGGER_ABANDONED_CART => "🌮 ¡{customer_name}, tu orden te espera!\n\nTienes \${cart_total} en tu carrito de {restaurant_name}.\n\nCompleta tu pedido: {order_url}\n\nReply STOP to unsubscribe",
            
            self::TRIGGER_WINBACK => "👋 ¡Te extrañamos {customer_name}!\n\nHan pasado {days_since_order} días. Aquí tienes {coupon_discount} OFF tu próximo pedido.\n\nCódigo: {coupon_code}\nOrdena: {order_url}\n\nReply STOP to unsubscribe",
            
            self::TRIGGER_BIRTHDAY => "🎂 ¡Feliz cumpleaños {customer_name}!\n\nTu regalo de {restaurant_name}: {coupon_discount} en tu pedido de hoy.\n\nCódigo: {coupon_code}\nOrdena: {order_url}\n\nReply STOP to unsubscribe",
            
            self::TRIGGER_LOYALTY_MILESTONE => "🎉 ¡Felicidades {customer_name}!\n\nTienes {points} puntos en {restaurant_name}.\n\nCanjea tus recompensas: {rewards_url}\n\nReply STOP to unsubscribe",
            
            self::TRIGGER_POST_ORDER => "✅ ¡Gracias por tu pedido #{order_number}!\n\n{restaurant_name}\nTotal: \${order_total}\n\nEstará listo a las {pickup_time}.\n\nReply STOP to unsubscribe",
            
            self::TRIGGER_REVIEW_REQUEST => "⭐ ¿Cómo estuvo tu comida de {restaurant_name}?\n\nDeja una reseña y gana {points_reward} puntos: {review_url}\n\nReply STOP to unsubscribe",
            
            self::TRIGGER_WELCOME => "🎉 ¡Bienvenido a {restaurant_name}!\n\nGracias por unirte. Usa código {coupon_code} para {coupon_discount} en tu primer pedido.\n\nOrdena: {order_url}\n\nReply STOP to unsubscribe",
            
            self::TRIGGER_POINTS_EXPIRING => "⚠️ {customer_name}, tienes {expiring_points} puntos por expirar en {days_until_expiry} días.\n\n¡Úsalos antes de perderlos!\n\nVer recompensas: {rewards_url}\n\nReply STOP to unsubscribe",
        ];
    }

    // Relationships
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(SmsLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByTrigger($query, string $trigger)
    {
        return $query->where('trigger_type', $trigger);
    }

    // Helpers
    public function incrementSends(): void
    {
        $this->increment('sends_count');
    }

    public function incrementClicks(): void
    {
        $this->increment('clicks_count');
    }

    public function recordConversion(float $amount): void
    {
        $this->increment('conversions_count');
        $this->increment('revenue_generated', $amount);
    }

    public function getClickRateAttribute(): float
    {
        return $this->sends_count > 0 
            ? round(($this->clicks_count / $this->sends_count) * 100, 1) 
            : 0;
    }

    public function getConversionRateAttribute(): float
    {
        return $this->sends_count > 0 
            ? round(($this->conversions_count / $this->sends_count) * 100, 1) 
            : 0;
    }
}

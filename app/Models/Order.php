<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'restaurant_id',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'order_type',
        'delivery_address',
        'delivery_city',
        'delivery_zip',
        'delivery_instructions',
        'scheduled_for',
        'subtotal',
        'tax',
        'delivery_fee',
        'tip',
        'discount',
        'total',
        'payment_method',
        'payment_status',
        'payment_intent_id',
        'status',
        'special_instructions',
        'cancellation_reason',
        'confirmed_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'tip' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'scheduled_for' => 'datetime',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Generate unique order number
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = now()->format('ymd');
        $random = strtoupper(substr(uniqid(), -4));
        return "{$prefix}{$date}{$random}";
    }

    // Relationships
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function scopeForRestaurant($query, $restaurantId)
    {
        return $query->where('restaurant_id', $restaurantId);
    }

    // Status helpers
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isPreparing(): bool
    {
        return $this->status === 'preparing';
    }

    public function isReady(): bool
    {
        return $this->status === 'ready';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    // Status labels
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => '⏳ Pendiente',
            'confirmed' => '✅ Confirmado',
            'preparing' => '👨‍🍳 Preparando',
            'ready' => '🔔 Listo',
            'out_for_delivery' => '🚗 En camino',
            'completed' => '✨ Completado',
            'cancelled' => '❌ Cancelado',
            default => $this->status,
        };
    }

    public function getOrderTypeLabelAttribute(): string
    {
        return match($this->order_type) {
            'pickup' => '🏃 Para llevar',
            'delivery' => '🚗 Delivery',
            'dine_in' => '🍽️ Comer aquí',
            default => $this->order_type,
        };
    }

    // Update status with timestamp
    public function updateStatus(string $status): void
    {
        $this->status = $status;
        
        if ($status === 'confirmed') {
            $this->confirmed_at = now();
        } elseif ($status === 'completed') {
            $this->completed_at = now();
        } elseif ($status === 'cancelled') {
            $this->cancelled_at = now();
        }
        
        $this->save();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PickupOrder extends Model
{
    protected $fillable = [
        'restaurant_id',
        'user_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'items',
        'subtotal',
        'tax',
        'discount',
        'total',
        'status',
        'pickup_time',
        'special_instructions',
        'payment_status',
        'payment_method',
    ];

    protected $casts = [
        'items' => 'array',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'pickup_time' => 'datetime',
    ];

    const STATUSES = [
        'pending' => 'Pendiente',
        'confirmed' => 'Confirmado',
        'preparing' => 'Preparando',
        'ready' => 'Listo para recoger',
        'picked_up' => 'Entregado',
        'cancelled' => 'Cancelado',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusNameAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'confirmed' => 'info',
            'preparing' => 'primary',
            'ready' => 'success',
            'picked_up' => 'gray',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(8));
            }
        });
    }

    public function updateStatus(string $status): void
    {
        $this->update(['status' => $status]);
        
        // TODO: Send notification to customer
        // Notification::send($this->user, new OrderStatusUpdated($this));
    }

    public function calculateTotals(): void
    {
        $subtotal = collect($this->items)->sum(function ($item) {
            return ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        });

        $this->subtotal = $subtotal;
        $this->tax = $subtotal * 0.0825; // 8.25% tax
        $this->total = $this->subtotal + $this->tax - $this->discount;
        $this->save();
    }
}

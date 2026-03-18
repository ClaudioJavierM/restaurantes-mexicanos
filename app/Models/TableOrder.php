<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableOrder extends Model
{
    protected $fillable = [
        'restaurant_id', 'table_id', 'order_number', 'customer_name',
        'items', 'subtotal', 'notes', 'status',
        'confirmed_at', 'ready_at',
    ];

    protected $casts = [
        'items'        => 'array',
        'subtotal'     => 'decimal:2',
        'confirmed_at' => 'datetime',
        'ready_at'     => 'datetime',
    ];

    public static array $statusLabels = [
        'pending'   => 'Nuevo',
        'confirmed' => 'Confirmado',
        'preparing' => 'En preparación',
        'ready'     => 'Listo',
        'delivered' => 'Entregado',
        'cancelled' => 'Cancelado',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public static function generateNumber(): string
    {
        return 'T' . now()->format('ymd') . strtoupper(substr(uniqid(), -4));
    }

    public function getItemCountAttribute(): int
    {
        return collect($this->items)->sum('quantity');
    }
}

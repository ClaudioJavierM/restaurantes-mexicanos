<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    protected $fillable = [
        'restaurant_id', 'name', 'category', 'unit',
        'current_stock', 'min_stock', 'cost_per_unit', 'notes',
    ];

    protected $casts = [
        'current_stock' => 'decimal:3',
        'min_stock'     => 'decimal:3',
        'cost_per_unit' => 'decimal:2',
    ];

    public static array $categories = [
        'produce'   => '🥦 Verduras y Frutas',
        'meat'      => '🥩 Carnes',
        'seafood'   => '🐟 Mariscos',
        'dairy'     => '🧀 Lácteos',
        'beverages' => '🥤 Bebidas',
        'dry_goods' => '🌾 Abarrotes',
        'supplies'  => '🧹 Insumos',
        'other'     => '📦 Otros',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->min_stock;
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'min_stock');
    }
}

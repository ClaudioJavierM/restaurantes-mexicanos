<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuAbTest extends Model
{
    protected $fillable = [
        'restaurant_id', 'menu_item_id', 'test_name',
        'variant_a_name', 'variant_a_description', 'variant_a_price',
        'variant_b_name', 'variant_b_description', 'variant_b_price',
        'views_a', 'views_b', 'orders_a', 'orders_b',
        'status', 'winner', 'started_at', 'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
        'variant_a_price' => 'decimal:2',
        'variant_b_price' => 'decimal:2',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function conversionRateA(): float
    {
        return $this->views_a > 0 ? round($this->orders_a / $this->views_a * 100, 1) : 0;
    }

    public function conversionRateB(): float
    {
        return $this->views_b > 0 ? round($this->orders_b / $this->views_b * 100, 1) : 0;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}

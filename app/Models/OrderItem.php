<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'menu_item_id',
        'name',
        'description',
        'unit_price',
        'quantity',
        'total_price',
        'modifiers',
        'special_instructions',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'modifiers' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    // Calculate total price based on quantity and modifiers
    public function calculateTotal(): float
    {
        $total = $this->unit_price * $this->quantity;
        
        // Add modifier prices
        if ($this->modifiers) {
            foreach ($this->modifiers as $modifier) {
                $total += ($modifier['price'] ?? 0) * $this->quantity;
            }
        }
        
        return $total;
    }

    // Format modifiers for display
    public function getModifiersTextAttribute(): string
    {
        if (!$this->modifiers || empty($this->modifiers)) {
            return '';
        }
        
        return collect($this->modifiers)
            ->map(fn($m) => $m['name'] . ($m['price'] > 0 ? ' (+$' . number_format($m['price'], 2) . ')' : ''))
            ->implode(', ');
    }
}

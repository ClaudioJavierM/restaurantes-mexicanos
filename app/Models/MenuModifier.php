<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuModifier extends Model
{
    protected $fillable = [
        'modifier_group_id',
        'name',
        'name_es',
        'price',
        'is_default',
        'is_available',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_default' => 'boolean',
        'is_available' => 'boolean',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(MenuModifierGroup::class, 'modifier_group_id');
    }

    public function getDisplayPriceAttribute(): string
    {
        if ($this->price > 0) {
            return '+$' . number_format($this->price, 2);
        }
        return '';
    }
}

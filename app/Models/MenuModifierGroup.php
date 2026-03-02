<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuModifierGroup extends Model
{
    protected $fillable = [
        'menu_item_id',
        'name',
        'name_es',
        'is_required',
        'min_selections',
        'max_selections',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function modifiers(): HasMany
    {
        return $this->hasMany(MenuModifier::class, 'modifier_group_id');
    }

    public function availableModifiers(): HasMany
    {
        return $this->hasMany(MenuModifier::class, 'modifier_group_id')->where('is_available', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_category_id',
        'name',
        'name_es',
        'description',
        'description_es',
        'price',
        'sale_price',
        'image',
        'dietary_tags',
        'is_available',
        'is_popular',
        'is_new',
        'calories',
        'prep_time',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'dietary_tags' => 'array',
        'is_available' => 'boolean',
        'is_popular' => 'boolean',
        'is_new' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }

    public function restaurant(): BelongsTo
    {
        return $this->category->restaurant();
    }

    public function modifierGroups(): HasMany
    {
        return $this->hasMany(MenuModifierGroup::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getDisplayPriceAttribute(): string
    {
        if ($this->sale_price && $this->sale_price < $this->price) {
            return '$' . number_format($this->sale_price, 2);
        }
        return '$' . number_format($this->price, 2);
    }

    public function hasDiscount(): bool
    {
        return $this->sale_price && $this->sale_price < $this->price;
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->hasDiscount()) {
            return null;
        }
        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    // Dietary tag helpers
    public function isVegetarian(): bool
    {
        return in_array('vegetarian', $this->dietary_tags ?? []);
    }

    public function isVegan(): bool
    {
        return in_array('vegan', $this->dietary_tags ?? []);
    }

    public function isGlutenFree(): bool
    {
        return in_array('gluten-free', $this->dietary_tags ?? []);
    }

    public function isSpicy(): bool
    {
        return in_array('spicy', $this->dietary_tags ?? []);
    }
    public static function getDietaryOptions(): array
    {
        return [
            'vegetarian' => '🥬 Vegetariano',
            'vegan' => '🌱 Vegano',
            'gluten-free' => '🌾 Sin Gluten',
            'dairy-free' => '🥛 Sin Lácteos',
            'spicy' => '🌶️ Picante',
        ];
    }
}

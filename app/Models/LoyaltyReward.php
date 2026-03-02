<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyReward extends Model
{
    protected $fillable = [
        'restaurant_id',
        'name',
        'description',
        'points_required',
        'reward_type',
        'reward_value',
        'free_item_name',
        'usage_limit',
        'redemption_count',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'reward_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(LoyaltyRedemption::class, 'reward_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->active()->where(function ($q) {
            $q->whereNull('usage_limit')
              ->orWhereRaw('redemption_count < usage_limit');
        });
    }

    public function isAvailable(): bool
    {
        if (!$this->is_active) return false;
        if ($this->usage_limit && $this->redemption_count >= $this->usage_limit) return false;
        return true;
    }

    public function getFormattedRewardAttribute(): string
    {
        return match($this->reward_type) {
            'discount_percentage' => $this->reward_value . '% de descuento',
            'discount_fixed' => '$' . number_format($this->reward_value, 2) . ' de descuento',
            'free_item' => $this->free_item_name ?? 'Articulo gratis',
            'custom' => $this->description ?? 'Recompensa especial',
        };
    }

    public static function getRewardTypes(): array
    {
        return [
            'discount_percentage' => 'Descuento (%)',
            'discount_fixed' => 'Descuento ($)',
            'free_item' => 'Articulo Gratis',
            'custom' => 'Personalizado',
        ];
    }
}

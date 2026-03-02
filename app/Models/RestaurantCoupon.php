<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class RestaurantCoupon extends Model
{
    protected $fillable = [
        'restaurant_id',
        'code',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'free_item_name',
        'minimum_purchase',
        'max_uses',
        'uses_count',
        'max_uses_per_user',
        'valid_from',
        'valid_until',
        'valid_days',
        'valid_time_start',
        'valid_time_end',
        'terms',
        'is_active',
        'show_on_profile',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'minimum_purchase' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'valid_days' => 'array',
        'valid_time_start' => 'datetime:H:i',
        'valid_time_end' => 'datetime:H:i',
        'is_active' => 'boolean',
        'show_on_profile' => 'boolean',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->where(function($q) {
                $q->whereNull('max_uses')
                   ->orWhereColumn('uses_count', '<', 'max_uses');
            });
    }

    public function scopeShowOnProfile($query)
    {
        return $query->where('show_on_profile', true);
    }

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->valid_from > now()) return false;
        if ($this->valid_until < now()) return false;
        if ($this->max_uses && $this->uses_count >= $this->max_uses) return false;
        
        return true;
    }

    public function getFormattedDiscountAttribute(): string
    {
        switch ($this->discount_type) {
            case 'percentage':
                return number_format($this->discount_value, 0) . '% OFF';
            case 'fixed':
                return '$' . number_format($this->discount_value, 2) . ' OFF';
            case 'bogo':
                return 'Buy 1 Get 1';
            case 'free_item':
                return $this->free_item_name ? $this->free_item_name . ' GRATIS' : 'Item GRATIS';
            default:
                return 'Descuento';
        }
    }

    public static function generateCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('code', $code)->exists());
        
        return $code;
    }

    public function incrementUses(): void
    {
        $this->increment('uses_count');
    }

    public function getDaysRemainingAttribute(): int
    {
        return max(0, now()->diffInDays($this->valid_until, false));
    }
}

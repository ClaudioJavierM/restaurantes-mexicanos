<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class FlashDeal extends Model
{
    protected $fillable = [
        'restaurant_id',
        'title',
        'title_en',
        'description',
        'description_en',
        'discount_type',
        'discount_value',
        'code',
        'starts_at',
        'ends_at',
        'max_redemptions',
        'current_redemptions',
        'is_active',
        'applicable_for',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->where(function ($q) {
                $q->whereNull('max_redemptions')
                  ->orWhereRaw('current_redemptions < max_redemptions');
            });
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('starts_at', '>', now());
    }

    public function getFormattedDiscountAttribute(): string
    {
        if ($this->discount_type === 'percentage') {
            return $this->discount_value . '% OFF';
        } elseif ($this->discount_type === 'fixed') {
            return '$' . number_format($this->discount_value, 2) . ' OFF';
        }
        return 'GRATIS';
    }

    public function getTimeRemainingAttribute(): string
    {
        if ($this->ends_at->isPast()) {
            return 'Expirado';
        }
        
        $diff = now()->diff($this->ends_at);
        
        if ($diff->days > 0) {
            return $diff->days . 'd ' . $diff->h . 'h';
        } elseif ($diff->h > 0) {
            return $diff->h . 'h ' . $diff->i . 'm';
        }
        return $diff->i . ' minutos';
    }

    public function isAvailable(): bool
    {
        return $this->is_active 
            && $this->starts_at <= now() 
            && $this->ends_at >= now()
            && (!$this->max_redemptions || $this->current_redemptions < $this->max_redemptions);
    }

    public function redeem(): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }
        
        $this->increment('current_redemptions');
        return true;
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($deal) {
            if (empty($deal->code)) {
                $deal->code = strtoupper(Str::random(8));
            }
        });
    }
}

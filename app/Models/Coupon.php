<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'restaurant_id',
        'title',
        'title_en',
        'description',
        'description_en',
        'code',
        'discount_type',
        'discount_value',
        'minimum_purchase',
        'maximum_discount',
        'valid_from',
        'valid_until',
        'usage_limit',
        'usage_count',
        'usage_limit_per_user',
        'applicable_days',
        'applicable_time_start',
        'applicable_time_end',
        'applicable_dine_in',
        'applicable_takeout',
        'applicable_delivery',
        'is_active',
        'is_featured',
        'requires_subscription',
        'terms',
        'terms_en',
        'views_count',
        'clicks_count',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'applicable_days' => 'array',
        'applicable_time_start' => 'datetime:H:i',
        'applicable_time_end' => 'datetime:H:i',
        'applicable_dine_in' => 'boolean',
        'applicable_takeout' => 'boolean',
        'applicable_delivery' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'requires_subscription' => 'boolean',
        'discount_value' => 'decimal:2',
        'minimum_purchase' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
    ];

    // Relationships
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('valid_from')
              ->orWhere('valid_from', '<=', now());
        })->where(function ($q) {
            $q->whereNull('valid_until')
              ->orWhere('valid_until', '>=', now());
        });
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('valid_until')
              ->orWhere('valid_until', '>=', now());
        });
    }

    public function scopeHasUsagesLeft($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('usage_limit')
              ->orWhereRaw('usage_count < usage_limit');
        });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Methods
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->valid_from && $this->valid_from->isFuture()) {
            return false;
        }

        if ($this->valid_until && $this->valid_until->isPast()) {
            return false;
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function incrementClicks(): void
    {
        $this->increment('clicks_count');
    }

    public function getFormattedDiscountAttribute(): string
    {
        if ($this->discount_type === 'percentage') {
            return $this->discount_value . '%';
        }

        return '$' . number_format($this->discount_value, 2);
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->valid_until) {
            return null;
        }

        return max(0, now()->diffInDays($this->valid_until, false));
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->valid_until && $this->valid_until->isPast();
    }

    public function getUsagePercentageAttribute(): ?float
    {
        if (!$this->usage_limit) {
            return null;
        }

        return ($this->usage_count / $this->usage_limit) * 100;
    }

    public function getLocalizedTitle(): string
    {
        if (app()->getLocale() === 'en' && $this->title_en) {
            return $this->title_en;
        }

        return $this->title;
    }

    public function getLocalizedDescription(): ?string
    {
        if (app()->getLocale() === 'en' && $this->description_en) {
            return $this->description_en;
        }

        return $this->description;
    }

    public function getLocalizedTerms(): ?string
    {
        if (app()->getLocale() === 'en' && $this->terms_en) {
            return $this->terms_en;
        }

        return $this->terms;
    }
}

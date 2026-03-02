<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionCoupon extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'duration',
        'duration_in_months',
        'max_redemptions',
        'times_redeemed',
        'expires_at',
        'stripe_coupon_id',
        'stripe_promotion_code_id',
        'is_active',
        'created_by',
        'metadata',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Check if coupon is still valid
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_redemptions && $this->times_redeemed >= $this->max_redemptions) {
            return false;
        }

        return true;
    }

    /**
     * Get formatted discount
     */
    public function getFormattedDiscountAttribute(): string
    {
        if ($this->discount_type === 'percentage') {
            return "{$this->discount_value}% OFF";
        }

        return "\${$this->discount_value} OFF";
    }

    /**
     * Get usage percentage
     */
    public function getUsagePercentageAttribute(): ?float
    {
        if (!$this->max_redemptions) {
            return null;
        }

        return ($this->times_redeemed / $this->max_redemptions) * 100;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriberCouponRedemption extends Model
{
    protected $fillable = [
        'subscriber_coupon_id',
        'business_code',
        'order_id',
        'order_total',
        'discount_applied',
        'customer_ip',
        'notes',
        'redeemed_at',
    ];

    protected $casts = [
        'order_total' => 'decimal:2',
        'discount_applied' => 'decimal:2',
        'redeemed_at' => 'datetime',
    ];

    // Relationships
    public function subscriberCoupon(): BelongsTo
    {
        return $this->belongsTo(SubscriberCoupon::class);
    }

    // Scopes
    public function scopeForBusiness($query, string $businessCode)
    {
        return $query->where('business_code', $businessCode);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('redeemed_at', '>=', now()->subDays($days));
    }

    // Get business name
    public function getBusinessNameAttribute(): string
    {
        $businesses = SubscriptionBenefit::getBusinesses();
        return $businesses[$this->business_code] ?? $this->business_code;
    }

    // Get formatted discount
    public function getFormattedDiscountAttribute(): string
    {
        return '$' . number_format($this->discount_applied ?? 0, 2);
    }

    // Statistics helpers
    public static function getTotalRedemptions(): int
    {
        return static::count();
    }

    public static function getTotalDiscountGiven(): float
    {
        return static::sum('discount_applied') ?? 0;
    }

    public static function getRedemptionsByBusiness(): array
    {
        return static::selectRaw('business_code, COUNT(*) as count, SUM(discount_applied) as total_discount')
            ->groupBy('business_code')
            ->get()
            ->keyBy('business_code')
            ->toArray();
    }
}

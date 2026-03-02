<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionBenefit extends Model
{
    protected $fillable = [
        'tier',
        'business_code',
        'business_name',
        'business_url',
        'business_logo',
        'discount_type',
        'discount_value',
        'min_purchase',
        'max_discount',
        'description',
        'includes_free_shipping',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'includes_free_shipping' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTier($query, string $tier)
    {
        return $query->where('tier', $tier);
    }

    public function scopeForBusiness($query, string $businessCode)
    {
        return $query->where('business_code', $businessCode);
    }

    // Helpers
    public function getFormattedDiscountAttribute(): string
    {
        if ($this->discount_type === 'percentage') {
            return intval($this->discount_value) . '% off';
        } elseif ($this->discount_type === 'fixed') {
            return '$' . number_format($this->discount_value, 2) . ' off';
        } else {
            return 'Envío Gratis';
        }
    }

    public function calculateDiscount(float $cartTotal): float
    {
        if ($cartTotal < $this->min_purchase) {
            return 0;
        }

        $discount = 0;

        if ($this->discount_type === 'percentage') {
            $discount = $cartTotal * ($this->discount_value / 100);
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
        } elseif ($this->discount_type === 'fixed') {
            $discount = min($this->discount_value, $cartTotal);
        }

        return round($discount, 2);
    }

    // Get all benefits for a tier, grouped by business
    public static function getBenefitsForTier(string $tier): array
    {
        return static::active()
            ->forTier($tier)
            ->orderBy('sort_order')
            ->get()
            ->keyBy('business_code')
            ->toArray();
    }

    // Get available tiers
    public static function getTiers(): array
    {
        return [
            'free' => 'Gratuito',
            'premium' => 'Premium',
            'elite' => 'Elite',
        ];
    }

    // Get available businesses
    public static function getBusinesses(): array
    {
        return [
            'mf_imports' => 'MF Imports',
            'tormex' => 'Tormex Pro',
            'mf_trailers' => 'MF Trailers',
            'muebles_mexicanos' => 'Muebles Mexicanos',
            'refrimex' => 'Refrimex Paletería',
        ];
    }
}

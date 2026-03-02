<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SubscriberCoupon extends Model
{
    protected $fillable = [
        'code',
        'restaurant_id',
        'user_id',
        'user_email',
        'user_name',
        'tier',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    // Relationships
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(SubscriberCouponRedemption::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                   ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Check if coupon can be used at a specific business
    public function canUseAtBusiness(string $businessCode): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        // Check if already used at this business
        return !$this->redemptions()
            ->where('business_code', $businessCode)
            ->exists();
    }

    // Check if already used at business
    public function isUsedAtBusiness(string $businessCode): bool
    {
        return $this->redemptions()
            ->where('business_code', $businessCode)
            ->exists();
    }

    // Get redemption for a business
    public function getRedemptionForBusiness(string $businessCode): ?SubscriberCouponRedemption
    {
        return $this->redemptions()
            ->where('business_code', $businessCode)
            ->first();
    }

    // Get benefit for a specific business
    public function getBenefitForBusiness(string $businessCode): ?SubscriptionBenefit
    {
        return SubscriptionBenefit::active()
            ->forTier($this->tier)
            ->forBusiness($businessCode)
            ->first();
    }

    // Get all benefits with usage status
    public function getBenefitsWithStatus(): array
    {
        $benefits = SubscriptionBenefit::active()
            ->forTier($this->tier)
            ->orderBy('sort_order')
            ->get();

        $redemptions = $this->redemptions->keyBy('business_code');

        return $benefits->map(function ($benefit) use ($redemptions) {
            $redemption = $redemptions->get($benefit->business_code);
            return [
                'business_code' => $benefit->business_code,
                'business_name' => $benefit->business_name,
                'business_url' => $benefit->business_url,
                'business_logo' => $benefit->business_logo,
                'discount_type' => $benefit->discount_type,
                'discount_value' => $benefit->discount_value,
                'formatted_discount' => $benefit->formatted_discount,
                'description' => $benefit->description,
                'includes_free_shipping' => $benefit->includes_free_shipping,
                'is_used' => $redemption !== null,
                'used_at' => $redemption?->redeemed_at,
                'order_id' => $redemption?->order_id,
            ];
        })->toArray();
    }

    // Validate coupon for a business
    public function validate(string $businessCode, float $cartTotal = 0): array
    {
        // Check if active
        if (!$this->is_active) {
            return [
                'valid' => false,
                'message' => 'Este cupón no está activo',
                'error_code' => 'inactive',
            ];
        }

        // Check expiration
        if ($this->expires_at && $this->expires_at->isPast()) {
            return [
                'valid' => false,
                'message' => 'Este cupón ha expirado',
                'error_code' => 'expired',
            ];
        }

        // Check if already used at this business
        if ($this->isUsedAtBusiness($businessCode)) {
            $redemption = $this->getRedemptionForBusiness($businessCode);
            return [
                'valid' => false,
                'message' => 'Este cupón ya fue usado en este negocio',
                'error_code' => 'already_used',
                'used_at' => $redemption->redeemed_at->toIso8601String(),
            ];
        }

        // Get benefit for this business
        $benefit = $this->getBenefitForBusiness($businessCode);
        if (!$benefit) {
            return [
                'valid' => false,
                'message' => 'No hay beneficio disponible para este negocio',
                'error_code' => 'no_benefit',
            ];
        }

        // Check minimum purchase
        if ($cartTotal > 0 && $cartTotal < $benefit->min_purchase) {
            return [
                'valid' => false,
                'message' => "Compra mínima requerida: \$" . number_format($benefit->min_purchase, 2),
                'error_code' => 'min_purchase',
                'min_purchase' => $benefit->min_purchase,
            ];
        }

        // Calculate discount
        $discountAmount = $cartTotal > 0 ? $benefit->calculateDiscount($cartTotal) : 0;

        return [
            'valid' => true,
            'discount_type' => $benefit->discount_type,
            'discount_value' => $benefit->discount_value,
            'discount_amount' => $discountAmount,
            'includes_free_shipping' => $benefit->includes_free_shipping,
            'message' => '¡Cupón válido! ' . $benefit->formatted_discount,
            'tier' => $this->tier,
            'subscriber_name' => $this->user_name,
        ];
    }

    // Redeem coupon at a business
    public function redeem(string $businessCode, ?string $orderId = null, ?float $orderTotal = null, ?float $discountApplied = null): SubscriberCouponRedemption
    {
        return $this->redemptions()->create([
            'business_code' => $businessCode,
            'order_id' => $orderId,
            'order_total' => $orderTotal,
            'discount_applied' => $discountApplied,
            'customer_ip' => request()->ip(),
        ]);
    }

    // Generate unique code
    public static function generateCode(Restaurant $restaurant): string
    {
        $prefix = 'FAMER';
        $restaurantCode = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $restaurant->name), 0, 3));
        $uniquePart = strtoupper(Str::random(5));
        
        $code = $prefix . "-" . $restaurantCode . $restaurant->id . "-" . $uniquePart;
        
        // Ensure uniqueness
        while (static::where('code', $code)->exists()) {
            $uniquePart = strtoupper(Str::random(5));
            $code = $prefix . "-" . $restaurantCode . $restaurant->id . "-" . $uniquePart;
        }
        
        return $code;
    }

    // Create coupon for a restaurant subscription
    public static function createForRestaurant(Restaurant $restaurant, User $user, string $tier = 'free'): self
    {
        // Check if user already has a coupon for this restaurant
        $existing = static::where('restaurant_id', $restaurant->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            // Update tier if upgrading
            $existing->update(['tier' => $tier]);
            return $existing;
        }

        return static::create([
            'code' => static::generateCode($restaurant),
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'tier' => $tier,
            'is_active' => true,
        ]);
    }
}

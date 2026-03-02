<?php

namespace App\Services;

use App\Models\MfImportsPromoCode;
use Stripe\Stripe;
use Stripe\PromotionCode;
use Exception;
use Illuminate\Support\Str;

class MfImportsPromoService
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret'));
    }
    
    /**
     * Generate a promo code for MF Imports customer
     * 
     * @param array $data [
     *   'customer_email' => required,
     *   'customer_name' => optional,
     *   'order_id' => required,
     *   'order_total' => required (decimal),
     *   'used_famer_discount' => optional (bool),
     * ]
     */
    public function generatePromoCode(array $data): ?MfImportsPromoCode
    {
        // Validate required fields
        if (empty($data['customer_email']) || empty($data['order_id']) || !isset($data['order_total'])) {
            throw new Exception('Missing required fields: customer_email, order_id, order_total');
        }
        
        $orderTotal = (float) $data['order_total'];
        $usedFamerDiscount = $data['used_famer_discount'] ?? false;
        
        // Check if customer used FAMER discount - if so, not eligible
        if ($usedFamerDiscount) {
            return null;
        }
        
        // Check if order already has a promo code
        $existing = MfImportsPromoCode::where('mf_order_id', $data['order_id'])->first();
        if ($existing) {
            return $existing;
        }
        
        // Determine tier based on order total
        $tier = null;
        $couponId = null;
        
        if ($orderTotal >= 40000) {
            $tier = '6_months';
            $couponId = config('stripe.coupon_6_months', env('STRIPE_COUPON_6_MONTHS'));
        } elseif ($orderTotal >= 10000) {
            $tier = '3_months';
            $couponId = config('stripe.coupon_3_months', env('STRIPE_COUPON_3_MONTHS'));
        } else {
            // Order total too low, not eligible
            return null;
        }
        
        // Generate unique promo code
        $promoCode = $this->generateUniqueCode($tier, $data['order_id']);
        
        // Create Stripe promotion code
        $stripePromoCodeId = null;
        try {
            $stripePromoCode = PromotionCode::create([
                'coupon' => $couponId,
                'code' => $promoCode,
                'max_redemptions' => 1, // Single use
                'metadata' => [
                    'mf_order_id' => $data['order_id'],
                    'mf_customer_email' => $data['customer_email'],
                    'tier' => $tier,
                ],
            ]);
            $stripePromoCodeId = $stripePromoCode->id;
        } catch (Exception $e) {
            // Log error but continue - we can create Stripe code later if needed
            \Log::error('Failed to create Stripe promo code: ' . $e->getMessage());
        }
        
        // Save to database
        $record = MfImportsPromoCode::create([
            'mf_customer_email' => $data['customer_email'],
            'mf_customer_name' => $data['customer_name'] ?? null,
            'mf_order_id' => $data['order_id'],
            'mf_order_total' => $orderTotal,
            'promo_code' => $promoCode,
            'stripe_promotion_code_id' => $stripePromoCodeId,
            'tier' => $tier,
            'used_famer_discount_on_order' => $usedFamerDiscount,
            'is_active' => true,
            'expires_at' => now()->addYear(), // Valid for 1 year
        ]);
        
        return $record;
    }
    
    /**
     * Generate unique promo code
     * Format: MF[3/6]M-[ORDER_SUFFIX]-[RANDOM]
     */
    protected function generateUniqueCode(string $tier, string $orderId): string
    {
        $prefix = $tier === '6_months' ? 'MF6M' : 'MF3M';
        $orderSuffix = substr(preg_replace('/[^A-Z0-9]/', '', strtoupper($orderId)), -4);
        $random = strtoupper(Str::random(4));
        
        $code = $prefix . '-' . $orderSuffix . '-' . $random;
        
        // Ensure uniqueness
        while (MfImportsPromoCode::where('promo_code', $code)->exists()) {
            $random = strtoupper(Str::random(4));
            $code = $prefix . '-' . $orderSuffix . '-' . $random;
        }
        
        return $code;
    }
    
    /**
     * Validate and retrieve a promo code
     */
    public function validatePromoCode(string $code): ?MfImportsPromoCode
    {
        $promo = MfImportsPromoCode::where('promo_code', strtoupper($code))->first();
        
        if (!$promo || !$promo->isValid()) {
            return null;
        }
        
        return $promo;
    }
    
    /**
     * Redeem a promo code for a restaurant
     */
    public function redeemPromoCode(string $code, int $restaurantId): bool
    {
        $promo = $this->validatePromoCode($code);
        
        if (!$promo) {
            return false;
        }
        
        $promo->update([
            'is_redeemed' => true,
            'redeemed_at' => now(),
            'redeemed_by_restaurant_id' => $restaurantId,
        ]);
        
        return true;
    }
    
    /**
     * Get all active promo codes for a customer email
     */
    public function getActiveCodesForCustomer(string $email): \Illuminate\Database\Eloquent\Collection
    {
        return MfImportsPromoCode::forCustomer($email)
            ->active()
            ->orderByDesc('created_at')
            ->get();
    }
}

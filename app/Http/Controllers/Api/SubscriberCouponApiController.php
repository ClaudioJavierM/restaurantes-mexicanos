<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubscriberCoupon;
use App\Models\SubscriptionBenefit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriberCouponApiController extends Controller
{
    /**
     * Validate a subscriber coupon for a specific business
     * 
     * POST /api/v1/subscriber-coupons/validate
     */
    public function validate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50',
            'business' => 'required|string|max:50',
            'cart_total' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $code = strtoupper(trim($request->code));
        $business = strtolower(trim($request->business));
        $cartTotal = $request->cart_total ?? 0;

        // Find coupon
        $coupon = SubscriberCoupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'Cupón no encontrado',
                'error_code' => 'not_found',
            ]);
        }

        // Validate coupon
        $result = $coupon->validate($business, $cartTotal);

        return response()->json($result);
    }

    /**
     * Redeem a subscriber coupon at a specific business
     * 
     * POST /api/v1/subscriber-coupons/redeem
     */
    public function redeem(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50',
            'business' => 'required|string|max:50',
            'order_id' => 'nullable|string|max:100',
            'order_total' => 'nullable|numeric|min:0',
            'discount_applied' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $code = strtoupper(trim($request->code));
        $business = strtolower(trim($request->business));

        // Find coupon
        $coupon = SubscriberCoupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Cupón no encontrado',
                'error_code' => 'not_found',
            ], 404);
        }

        // Check if can be used
        if (!$coupon->canUseAtBusiness($business)) {
            $existingRedemption = $coupon->getRedemptionForBusiness($business);
            return response()->json([
                'success' => false,
                'message' => 'Este cupón ya fue usado en este negocio',
                'error_code' => 'already_used',
                'used_at' => $existingRedemption?->redeemed_at?->toIso8601String(),
            ], 409);
        }

        // Redeem
        try {
            $redemption = $coupon->redeem(
                $business,
                $request->order_id,
                $request->order_total,
                $request->discount_applied
            );

            return response()->json([
                'success' => true,
                'message' => 'Cupón redimido exitosamente',
                'redemption_id' => $redemption->id,
                'redeemed_at' => $redemption->redeemed_at->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            // Unique constraint violation = already used
            return response()->json([
                'success' => false,
                'message' => 'Este cupón ya fue usado en este negocio',
                'error_code' => 'already_used',
            ], 409);
        }
    }

    /**
     * Get coupon info with all benefits status
     * 
     * GET /api/v1/subscriber-coupons/{code}
     */
    public function show(string $code): JsonResponse
    {
        $code = strtoupper(trim($code));
        $coupon = SubscriberCoupon::with('redemptions')->where('code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'found' => false,
                'message' => 'Cupón no encontrado',
            ], 404);
        }

        return response()->json([
            'found' => true,
            'code' => $coupon->code,
            'tier' => $coupon->tier,
            'subscriber_name' => $coupon->user_name,
            'is_active' => $coupon->is_active,
            'expires_at' => $coupon->expires_at?->toIso8601String(),
            'benefits' => $coupon->getBenefitsWithStatus(),
        ]);
    }

    /**
     * Get available benefits for a tier
     * 
     * GET /api/v1/subscription-benefits/{tier}
     */
    public function benefits(string $tier): JsonResponse
    {
        if (!in_array($tier, ['free', 'premium', 'elite'])) {
            return response()->json([
                'success' => false,
                'message' => 'Tier inválido',
            ], 400);
        }

        $benefits = SubscriptionBenefit::active()
            ->forTier($tier)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'tier' => $tier,
            'benefits' => $benefits,
        ]);
    }
}

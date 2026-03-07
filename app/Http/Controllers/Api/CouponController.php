<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\UserCoupon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CouponController extends Controller
{
    /**
     * GET /v1/coupons
     * All active, valid coupons across all restaurants (for the consumer app browse tab).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Coupon::query()
            ->active()
            ->valid()
            ->with(['restaurant:id,name,slug,image'])
            ->orderBy('created_at', 'desc');

        if ($request->has('restaurant_id')) {
            $query->where('restaurant_id', $request->restaurant_id);
        }

        $coupons = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $coupons->items(),
            'meta'    => [
                'current_page' => $coupons->currentPage(),
                'last_page'    => $coupons->lastPage(),
                'total'        => $coupons->total(),
            ],
        ]);
    }

    /**
     * GET /v1/restaurants/{restaurantId}/coupons
     * Active coupons for a specific restaurant.
     */
    public function restaurantCoupons(Request $request, int $restaurantId): JsonResponse
    {
        $coupons = Coupon::where('restaurant_id', $restaurantId)
            ->active()
            ->valid()
            ->with(['restaurant:id,name,slug,image'])
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $coupons,
        ]);
    }

    /**
     * GET /v1/user/coupons
     * Coupons the authenticated user has claimed.
     */
    public function userCoupons(Request $request): JsonResponse
    {
        $userCoupons = UserCoupon::where('user_id', $request->user()->id)
            ->with(['coupon.restaurant:id,name,slug,image'])
            ->orderBy('claimed_at', 'desc')
            ->get()
            ->map(function ($uc) {
                $coupon = $uc->coupon;
                return [
                    'id'         => $uc->id,
                    'claimed_at' => $uc->claimed_at,
                    'used_at'    => $uc->used_at,
                    'is_used'    => $uc->is_used,
                    'coupon'     => $this->formatCoupon($coupon),
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $userCoupons,
        ]);
    }

    /**
     * POST /v1/coupons/{id}/claim
     * Claim a coupon — saves it to the user's wallet.
     */
    public function claim(Request $request, int $couponId): JsonResponse
    {
        $coupon = Coupon::active()->valid()->findOrFail($couponId);

        $existing = UserCoupon::where('user_id', $request->user()->id)
            ->where('coupon_id', $couponId)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Ya tienes este cupón en tu cartera',
            ], 422);
        }

        $userCoupon = UserCoupon::create([
            'user_id'    => $request->user()->id,
            'coupon_id'  => $couponId,
            'claimed_at' => now(),
            'is_used'    => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cupón guardado en tu cartera',
            'data'    => [
                'id'         => $userCoupon->id,
                'claimed_at' => $userCoupon->claimed_at,
                'used_at'    => null,
                'is_used'    => false,
                'coupon'     => $this->formatCoupon($coupon),
            ],
        ], 201);
    }

    /**
     * Format a Coupon model to match Coupon.fromJson() in Flutter.
     */
    private function formatCoupon(Coupon $coupon): array
    {
        return [
            'id'              => $coupon->id,
            'restaurant_id'   => $coupon->restaurant_id,
            'restaurant_name' => $coupon->restaurant?->name ?? '',
            'restaurant_image'=> $coupon->restaurant?->image,
            'code'            => $coupon->code,
            'title'           => $coupon->title,
            'description'     => $coupon->description,
            'discount_type'   => $coupon->discount_type,
            'discount_value'  => (float) $coupon->discount_value,
            'min_order_amount'=> $coupon->minimum_purchase ? (float) $coupon->minimum_purchase : null,
            'valid_from'      => $coupon->valid_from?->toDateString(),
            'valid_until'     => $coupon->valid_until?->toDateString(),
            'max_uses'        => $coupon->usage_limit,
            'uses_count'      => $coupon->usage_count ?? 0,
            'is_active'       => $coupon->is_active,
            'created_at'      => $coupon->created_at,
        ];
    }
}

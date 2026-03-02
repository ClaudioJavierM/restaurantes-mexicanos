<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MfImportsPromoService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MfImportsPromoController extends Controller
{
    protected MfImportsPromoService $promoService;
    
    public function __construct(MfImportsPromoService $promoService)
    {
        $this->promoService = $promoService;
    }
    
    /**
     * Generate a promo code for MF Imports order
     * 
     * POST /api/mf-imports/generate-promo
     * Headers: X-MF-API-Key: [secret_key]
     * Body: {
     *   "customer_email": "email@example.com",
     *   "customer_name": "John Doe",
     *   "order_id": "MF-12345",
     *   "order_total": 15000.00,
     *   "used_famer_discount": false
     * }
     */
    public function generatePromo(Request $request): JsonResponse
    {
        // Verify API key
        $apiKey = $request->header('X-MF-API-Key');
        if ($apiKey !== config('services.mf_imports.api_key')) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid API key',
            ], 401);
        }
        
        // Validate request
        $validated = $request->validate([
            'customer_email' => 'required|email',
            'customer_name' => 'nullable|string|max:255',
            'order_id' => 'required|string|max:100',
            'order_total' => 'required|numeric|min:0',
            'used_famer_discount' => 'nullable|boolean',
        ]);
        
        try {
            $promo = $this->promoService->generatePromoCode([
                'customer_email' => $validated['customer_email'],
                'customer_name' => $validated['customer_name'] ?? null,
                'order_id' => $validated['order_id'],
                'order_total' => $validated['order_total'],
                'used_famer_discount' => $validated['used_famer_discount'] ?? false,
            ]);
            
            if (!$promo) {
                return response()->json([
                    'success' => true,
                    'eligible' => false,
                    'message' => 'Order does not qualify for FAMER promo (requires $10k+ without FAMER discount)',
                ]);
            }
            
            return response()->json([
                'success' => true,
                'eligible' => true,
                'promo_code' => $promo->promo_code,
                'tier' => $promo->tier,
                'months_free' => $promo->months_free,
                'value_description' => $promo->value_description,
                'expires_at' => $promo->expires_at?->toISOString(),
                'message' => "Customer qualifies for {$promo->months_free} months free FAMER subscription",
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 422);
        }
    }
    
    /**
     * Validate a promo code
     * 
     * GET /api/mf-imports/validate-promo/{code}
     */
    public function validatePromo(string $code): JsonResponse
    {
        $promo = $this->promoService->validatePromoCode($code);
        
        if (!$promo) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid or expired promo code',
            ]);
        }
        
        return response()->json([
            'valid' => true,
            'promo_code' => $promo->promo_code,
            'tier' => $promo->tier,
            'months_free' => $promo->months_free,
            'value_description' => $promo->value_description,
            'customer_email' => $promo->mf_customer_email,
        ]);
    }
    
    /**
     * Get active promos for a customer
     * 
     * GET /api/mf-imports/customer-promos/{email}
     * Headers: X-MF-API-Key: [secret_key]
     */
    public function customerPromos(Request $request, string $email): JsonResponse
    {
        // Verify API key
        $apiKey = $request->header('X-MF-API-Key');
        if ($apiKey !== config('services.mf_imports.api_key')) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid API key',
            ], 401);
        }
        
        $promos = $this->promoService->getActiveCodesForCustomer($email);
        
        return response()->json([
            'success' => true,
            'customer_email' => $email,
            'active_promos' => $promos->map(fn($p) => [
                'promo_code' => $p->promo_code,
                'tier' => $p->tier,
                'months_free' => $p->months_free,
                'order_id' => $p->mf_order_id,
                'order_total' => $p->mf_order_total,
                'expires_at' => $p->expires_at?->toISOString(),
            ]),
        ]);
    }
}

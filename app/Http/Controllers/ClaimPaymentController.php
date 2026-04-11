<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClaimPaymentController extends Controller
{
    public function show(Request $request)
    {
        $restaurantSlug = $request->get('restaurant');
        $plan = $request->get('plan', 'premium');

        // Validate plan
        if (!in_array($plan, ['premium', 'elite'])) {
            $plan = 'premium';
        }

        $restaurant = Restaurant::where('slug', $restaurantSlug)
            ->where('status', 'approved')
            ->first();

        if (!$restaurant) {
            return redirect()->route('claim.restaurant')->with('error', 'Restaurante no encontrado.');
        }

        try {
            $stripeService = new StripeService();
            $coupon = session('claim_coupon_code');

            $session = $stripeService->createEmbeddedCheckoutSession(
                $restaurant,
                $plan,
                route('claim.success') . '?session_id={CHECKOUT_SESSION_ID}',
                $coupon
            );

            $planDetails = [
                'premium' => [
                    'name'     => 'Premium',
                    'price'    => '$9.99',
                    'period'   => 'primer mes',
                    'renewal'  => 'Después $39/mes',
                    'features' => [
                        'Perfil verificado con distintivo Premium',
                        'Posicionamiento destacado en búsquedas',
                        'Analytics avanzados de visitas',
                        'Herramientas de SEO y marketing',
                        'Cupones y promociones para clientes',
                    ],
                ],
                'elite' => [
                    'name'     => 'Elite',
                    'price'    => '$79',
                    'period'   => 'por mes',
                    'renewal'  => 'Renovación mensual',
                    'features' => [
                        'Todo lo de Premium',
                        'Distintivo Elite exclusivo',
                        'Prioridad #1 en resultados',
                        'Gestión de reseñas avanzada',
                        'Soporte dedicado prioritario',
                    ],
                ],
            ];

            return view('claim.payment', [
                'restaurant'     => $restaurant,
                'plan'           => $plan,
                'planDetails'    => $planDetails[$plan],
                'clientSecret'   => $session->client_secret,
                'stripePublicKey' => config('stripe.key'),
            ]);
        } catch (\Exception $e) {
            Log::error('Embedded checkout session error: ' . $e->getMessage());
            return redirect()
                ->route('claim.restaurant', ['restaurant' => $restaurantSlug])
                ->with('error', 'Error al inicializar el pago. Por favor intenta de nuevo.');
        }
    }
}

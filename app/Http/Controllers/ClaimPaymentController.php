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

            $result = $stripeService->createPendingSubscription($restaurant, $plan, $coupon);

            $planDetails = [
                'premium' => [
                    'name'     => 'Premium',
                    'price'    => '$9.99',
                    'period'   => 'primer mes',
                    'renewal'  => 'Después $39/mes',
                    'badge'    => 'Más popular',
                    'features' => [
                        'Badge Destacado en el directorio',
                        'Top 3 en búsquedas locales',
                        'Menú Digital + QR Code',
                        'Sistema de Reservaciones',
                        'Dashboard de Analíticas',
                        'Chatbot AI (ES/EN) 24/7',
                    ],
                ],
                'elite' => [
                    'name'     => 'Elite',
                    'price'    => 'Gratis',
                    'period'   => '30 días',
                    'renewal'  => 'Después $79/mes — cancela cuando quieras',
                    'badge'    => '30 días gratis',
                    'features' => [
                        'Todo lo de Premium PLUS:',
                        'App Móvil White Label',
                        'Website Builder Completo',
                        'Máxima Prioridad en Búsquedas',
                        'Account Manager Dedicado',
                        'Fotografía Profesional Trimestral',
                        'Cobertura de Medios y PR',
                    ],
                ],
            ];

            return view('claim.payment', [
                'restaurant'      => $restaurant,
                'plan'            => $plan,
                'planDetails'     => $planDetails[$plan],
                'clientSecret'    => $result['client_secret'],
                'subscriptionId'  => $result['subscription_id'],
                'isTrial'         => $result['is_trial'],
                'trialDays'       => $result['trial_days'],
                'stripePublicKey' => config('stripe.key'),
                'returnUrl'       => route('claim.success') . '?session_id=' . $result['subscription_id'],
            ]);
        } catch (\Exception $e) {
            \Log::error('Stripe Elements payment error: ' . $e->getMessage());
            return redirect()
                ->route('claim.restaurant', ['restaurant' => $restaurantSlug])
                ->with('error', 'Error al inicializar el pago. Por favor intenta de nuevo.');
        }
    }
}

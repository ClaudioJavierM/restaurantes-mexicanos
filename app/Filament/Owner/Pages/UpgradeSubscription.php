<?php

namespace App\Filament\Owner\Pages;

use App\Models\Restaurant;
use App\Services\StripeService;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class UpgradeSubscription extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-circle';
    protected static ?string $navigationLabel = 'Actualizar Plan';
    protected static ?string $title = 'Actualizar mi Plan';
    protected static ?string $navigationGroup = 'Configuracion';
    protected static ?int $navigationSort = 11;

    protected static string $view = 'filament.owner.pages.upgrade-subscription';

    public $restaurant = null;
    public $currentPlan = 'free';
    public $plans = [];

    public function mount(): void
    {
        $user = Auth::user();
        $this->restaurant = $user->allAccessibleRestaurants()->first();

        if ($this->restaurant) {
            $this->currentPlan = $this->restaurant->subscription_tier ?? 'free';
        }

        $this->plans = [
            'free' => [
                'name' => 'Gratis',
                'price' => 0,
                'price_id' => null,
                'features' => [
                    'Listado basico en el directorio',
                    'Informacion de contacto',
                    'Horarios de operacion',
                    '5% descuento en negocios FAMER',
                ],
                'discount' => '5%',
            ],
            'premium' => [
                'name' => 'Premium',
                'price' => 39,
                'first_month_price' => 9.99,
                'price_id' => config('stripe.prices.premium', 'price_premium'),
                'features' => [
                    'Todo lo del plan Gratis',
                    'Badge Premium verificado',
                    'Galeria de fotos ilimitada',
                    'Menu digital completo',
                    'Analiticas basicas',
                    '10% descuento en negocios FAMER',
                ],
                'discount' => '10%',
            ],
            'elite' => [
                'name' => 'Elite',
                'price' => 79,
                'first_month_price' => 9.99,
                'price_id' => config('stripe.prices.elite', 'price_elite'),
                'features' => [
                    'Todo lo del plan Premium',
                    'Badge Elite destacado',
                    'Posicion prioritaria en busquedas',
                    'Sistema de reservaciones',
                    'Chatbot para clientes',
                    'Email marketing',
                    'Cupones y promociones',
                    'Analiticas avanzadas',
                    '15% descuento en negocios FAMER',
                ],
                'discount' => '15%',
            ],
        ];
    }

    public function upgradeToPlan(string $plan): void
    {
        if (!$this->restaurant || $plan === $this->currentPlan) {
            return;
        }

        // If downgrading to free
        if ($plan === 'free') {
            $this->restaurant->update([
                'subscription_tier' => 'free',
                'subscription_status' => 'active',
                'premium_badge' => false,
                'premium_analytics' => false,
                'premium_menu' => false,
                'premium_reservations' => false,
                'premium_chatbot' => false,
                'premium_email_marketing' => false,
                'premium_coupons' => false,
            ]);

            // Update coupon tier
            if ($coupon = $this->restaurant->subscriberCoupon) {
                $coupon->update(['tier' => 'free']);
            }

            session()->flash('success', 'Tu plan ha sido actualizado a Gratis.');
            $this->currentPlan = 'free';
            return;
        }

        // For paid plans, redirect to Stripe Checkout
        try {
            $stripeService = new StripeService();
            
            $session = $stripeService->createUpgradeCheckoutSession(
                $this->restaurant,
                $plan,
                route('owner.upgrade.success') . '?session_id={CHECKOUT_SESSION_ID}',
                route('owner.upgrade.cancel')
            );

            redirect($session->url);
        } catch (\Exception $e) {
            session()->flash('error', 'Error al procesar: ' . $e->getMessage());
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        $restaurant = $user->allAccessibleRestaurants()->first();
        return $restaurant && $restaurant->is_claimed;
    }
}

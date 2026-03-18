<?php

namespace App\Filament\Owner\Pages;

use Filament\Pages\Page;
use App\Services\StripeService;
use Illuminate\Support\Facades\Auth;

class MySubscription extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Mi Suscripción';
    protected static ?string $title = 'Mi Plan de Suscripción';
    protected static ?string $navigationGroup = 'Configuracion';
    protected static ?int $navigationSort = 10;
    
    protected static string $view = 'filament.owner.pages.my-subscription';

    public $restaurant;
    public $currentPlan;
    public $planDetails;

    protected $plans = [
        'free' => [
            'name' => 'Gratis',
            'price' => 0,
            'color' => 'green',
            'features' => [
                'Perfil verificado con badge',
                'Editar información básica',
                'Responder a reseñas',
                'Horarios y contacto',
                'Hasta 5 fotos',
            ],
            'not_included' => [
                'Insignia Destacada',
                'Analytics Completos',
                'Menú Digital + Código QR',
                'Widget de Pedidos Online',
                'Sistema de Reservas',
                'Chatbot IA Bilingüe',
            ],
        ],
        'premium' => [
            'name' => 'Premium',
            'price' => 39,
            'color' => 'red',
            'features' => [
                'Todo en Gratis +',
                'Insignia Destacada',
                'Analytics Completos',
                'Menú Digital + Código QR',
                'Widget de Pedidos Online',
                'Sistema de Reservas',
                'Chatbot IA Bilingüe',
                'Hasta 25 fotos',
            ],
            'not_included' => [
                'Posición #1 en tu Ciudad',
                'App Móvil Marca Blanca',
                'Sitio Web Completo',
            ],
        ],
        'elite' => [
            'name' => 'Elite',
            'price' => 79,
            'color' => 'yellow',
            'features' => [
                'Todo en Premium +',
                'Posición #1 en tu Ciudad',
                'App Móvil Marca Blanca',
                'Sitio Web Completo',
                'Fotografía Profesional',
                'Gerente de Cuenta Dedicado',
                'Fotos Ilimitadas',
            ],
            'not_included' => [],
        ],
    ];

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        return $user->restaurants()->exists();
    }

    public static function canAccess(): bool
    {
        return static::shouldRegisterNavigation();
    }

    public function mount(): void
    {
        $this->restaurant = Auth::user()->allAccessibleRestaurants()->first();
        $this->currentPlan = $this->restaurant?->subscription_tier ?? 'free';
        $this->planDetails = $this->plans[$this->currentPlan] ?? $this->plans['free'];
    }

    public function getPlans(): array
    {
        return $this->plans;
    }

    public function upgradePlan(string $plan): void
    {
        if ($plan === $this->currentPlan || !$this->restaurant) {
            return;
        }

        try {
            $stripeService = new StripeService();
            
            $session = $stripeService->createCheckoutSession(
                $this->restaurant,
                $plan,
                route('filament.owner.pages.my-subscription') . '?upgraded=1',
                route('filament.owner.pages.my-subscription') . '?cancelled=1'
            );

            redirect($session->url);
        } catch (\Exception $e) {
            session()->flash('error', 'Error al procesar: ' . $e->getMessage());
        }
    }

    public function manageBilling(): void
    {
        if (!$this->restaurant || !$this->restaurant->stripe_customer_id) {
            session()->flash('error', 'No tienes un perfil de facturación activo. Contacta soporte@restaurantesmexicanosfamosos.com');
            return;
        }

        try {
            $stripeService = new StripeService();
            $returnUrl = route('filament.owner.pages.my-subscription');
            $portalUrl = $stripeService->createBillingPortalSession(
                $this->restaurant->stripe_customer_id,
                $returnUrl
            );
            redirect($portalUrl);
        } catch (\Exception $e) {
            session()->flash('error', 'Error al acceder al portal de facturación: ' . $e->getMessage());
        }
    }

    public function cancelSubscription(): void
    {
        session()->flash('info', 'Para cancelar tu suscripción, usa el botón "Administrar Facturación" o contáctanos a soporte@restaurantesmexicanosfamosos.com');
    }
}

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
    public array $paymentHistory = [];

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
        $this->restaurant = Auth::user()->firstAccessibleRestaurant();
        $this->currentPlan = $this->restaurant?->subscription_tier ?? 'free';
        $this->planDetails = $this->plans[$this->currentPlan] ?? $this->plans['free'];
        $this->loadPaymentHistory();
    }

    public function loadPaymentHistory(): void
    {
        if (!$this->restaurant?->stripe_customer_id) {
            return;
        }

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $invoices = \Stripe\Invoice::all([
                'customer' => $this->restaurant->stripe_customer_id,
                'limit'    => 12,
            ]);

            $this->paymentHistory = collect($invoices->data)->map(function ($inv) {
                return [
                    'invoice_id'       => $inv->id,
                    'payment_intent'   => $inv->payment_intent ?? null,
                    'amount'           => number_format($inv->amount_paid / 100, 2),
                    'currency'         => strtoupper($inv->currency),
                    'status'           => $inv->status,
                    'date'             => \Carbon\Carbon::createFromTimestamp($inv->created)->format('M d, Y'),
                    'description'      => $inv->lines->data[0]->description ?? 'Subscription',
                    'pdf_url'          => $inv->invoice_pdf,
                    'hosted_url'       => $inv->hosted_invoice_url,
                ];
            })->toArray();
        } catch (\Exception $e) {
            $this->paymentHistory = [];
        }
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

    public function openBillingPortal(): mixed
    {
        $restaurant = auth()->user()->firstAccessibleRestaurant();

        if (!$restaurant || !$restaurant->stripe_customer_id) {
            \Filament\Notifications\Notification::make()
                ->title('No tienes una suscripción activa de Stripe')
                ->warning()
                ->send();
            return null;
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $session = \Stripe\BillingPortal\Session::create([
            'customer'   => $restaurant->stripe_customer_id,
            'return_url' => route('filament.owner.pages.my-subscription'),
        ]);

        return redirect($session->url);
    }
}

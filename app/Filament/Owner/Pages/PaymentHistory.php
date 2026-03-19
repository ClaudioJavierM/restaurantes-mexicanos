<?php

namespace App\Filament\Owner\Pages;

use App\Services\StripeService;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class PaymentHistory extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationLabel = 'Historial de Pagos';
    protected static ?string $title = 'Historial de Pagos';
    protected static ?string $navigationGroup = 'Configuracion';
    protected static ?int $navigationSort = 12;

    protected static string $view = 'filament.owner.pages.payment-history';

    public $restaurant = null;
    public array $invoices = [];
    public $upcomingInvoice = null;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        $restaurant = $user->allAccessibleRestaurants()->first();
        return $restaurant &&
            $restaurant->is_claimed &&
            $restaurant->stripe_customer_id &&
            in_array($restaurant->subscription_tier, ['premium', 'elite']);
    }

    public function mount(): void
    {
        $user = Auth::user();
        $this->restaurant = $user->allAccessibleRestaurants()->first();

        if (!$this->restaurant || !$this->restaurant->stripe_customer_id) {
            return;
        }

        try {
            $stripeService = new StripeService();
            $rawInvoices = $stripeService->getInvoices($this->restaurant->stripe_customer_id);
            $this->invoices = array_map(fn($inv) => is_object($inv) ? $inv->toArray() : $inv, $rawInvoices);
            $upcoming = $stripeService->getUpcomingInvoice($this->restaurant->stripe_customer_id);
            $this->upcomingInvoice = $upcoming ? $upcoming->toArray() : null;
        } catch (\Exception $e) {
            $this->invoices = [];
            $this->upcomingInvoice = null;
        }
    }

    public function openBillingPortal(): void
    {
        if (!$this->restaurant || !$this->restaurant->stripe_customer_id) {
            return;
        }

        try {
            $stripeService = new StripeService();
            $portalUrl = $stripeService->createBillingPortalSession(
                $this->restaurant->stripe_customer_id,
                route('filament.owner.pages.payment-history')
            );
            redirect($portalUrl);
        } catch (\Exception $e) {
            session()->flash('error', 'Error al acceder al portal: ' . $e->getMessage());
        }
    }
}

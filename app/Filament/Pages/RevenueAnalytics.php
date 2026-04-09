<?php

namespace App\Filament\Pages;

use App\Models\Restaurant;
use Carbon\Carbon;
use Filament\Pages\Page;

class RevenueAnalytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Revenue Analytics';

    protected static ?string $navigationGroup = 'Ingresos';

    protected static ?int $navigationSort = 2;

    protected static bool $isLazy = true;

    protected static string $view = 'filament.pages.revenue-analytics';

    // ─── Computed Properties ─────────────────────────────────────────────────

    public int $premiumActive = 0;
    public int $eliteActive   = 0;
    public int $claimedCount  = 0;
    public int $freeCount     = 0;
    public int $totalActive   = 0;
    public int $mrr           = 0;
    public int $arr           = 0;
    public int $churnRisk     = 0;
    public int $totalApproved = 0;
    public int $totalClaimed  = 0;
    public int $withSub       = 0;
    public float $claimRate   = 0;
    public float $subRate     = 0;
    public float $eliteRate   = 0;

    /** @var \Illuminate\Support\Collection */
    public $expiringRestaurants;

    // ─── Mount ───────────────────────────────────────────────────────────────

    public function mount(): void
    {
        try {
            $this->premiumActive = Restaurant::where('subscription_tier', 'premium')
                ->where('subscription_status', 'active')
                ->count();

            $this->eliteActive = Restaurant::where('subscription_tier', 'elite')
                ->where('subscription_status', 'active')
                ->count();

            $this->claimedCount = Restaurant::where('subscription_tier', 'claimed')
                ->where('subscription_status', 'active')
                ->count();

            $this->freeCount = Restaurant::where('status', 'approved')
                ->where('is_claimed', false)
                ->whereNull('subscription_tier')
                ->count();

            $this->totalActive = Restaurant::whereNotNull('subscription_tier')
                ->where('subscription_status', 'active')
                ->count();

            $this->mrr = ($this->premiumActive * 29) + ($this->eliteActive * 79);
            $this->arr = $this->mrr * 12;

            $this->churnRisk = Restaurant::whereNotNull('subscription_tier')
                ->whereBetween('subscription_expires_at', [now(), now()->addDays(30)])
                ->count();

            // Funnel
            $this->totalApproved = Restaurant::where('status', 'approved')->count();
            $this->totalClaimed  = Restaurant::where('is_claimed', true)->count();
            $this->withSub       = Restaurant::whereNotNull('subscription_tier')->count();

            $this->claimRate = $this->totalApproved > 0
                ? round(($this->totalClaimed / $this->totalApproved) * 100, 1) : 0;

            $this->subRate = $this->totalClaimed > 0
                ? round(($this->withSub / $this->totalClaimed) * 100, 1) : 0;

            $this->eliteRate = $this->withSub > 0
                ? round(($this->eliteActive / $this->withSub) * 100, 1) : 0;

            // Expiring in next 30 days
            $this->expiringRestaurants = Restaurant::with('state')
                ->whereNotNull('subscription_tier')
                ->whereBetween('subscription_expires_at', [now(), now()->addDays(30)])
                ->orderBy('subscription_expires_at')
                ->get(['id', 'name', 'owner_name', 'owner_email', 'owner_phone',
                       'subscription_tier', 'subscription_status', 'subscription_expires_at', 'state_id']);

        } catch (\Throwable $e) {
            $this->expiringRestaurants = collect();
        }
    }
}

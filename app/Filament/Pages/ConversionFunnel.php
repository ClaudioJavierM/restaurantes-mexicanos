<?php

namespace App\Filament\Pages;

use App\Models\Restaurant;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class ConversionFunnel extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-funnel';

    protected static ?string $navigationLabel = 'Conversion Funnel';

    protected static ?string $navigationGroup = 'Analytics';

    protected static ?int $navigationSort = 5;

    protected static bool $isLazy = true;

    protected static string $view = 'filament.pages.conversion-funnel';

    // ─── Public Properties ───────────────────────────────────────────────────

    public int $period = 30;

    public array $funnel = [];

    public array $cohorts = [];

    public array $abandonByStep = [];

    public array $revenueStats = [];

    public array $conversionByCountry = [];

    public array $dailySignups = [];

    // ─── Lifecycle ───────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->period = (int) request()->query('period', 30);
        $this->loadData();
    }

    // ─── Data Loading ─────────────────────────────────────────────────────────

    public function loadData(): void
    {
        $this->funnel              = $this->computeFunnel();
        $this->abandonByStep       = $this->computeAbandonByStep();
        $this->revenueStats        = $this->computeRevenueStats();
        $this->conversionByCountry = $this->computeConversionByCountry();
        $this->cohorts             = $this->computeCohorts();
        $this->dailySignups        = $this->computeDailySignups();
    }

    // ─── Funnel ──────────────────────────────────────────────────────────────

    private function computeFunnel(): array
    {
        try {
            $steps = [];

            $totalApproved = Restaurant::where('status', 'approved')->count();
            $steps[] = [
                'label' => 'Restaurantes Aprobados',
                'count' => $totalApproved,
                'pct'   => 100.0,
                'drop'  => 0.0,
            ];

            $haveEmail = Restaurant::where('status', 'approved')
                ->whereNotNull('email')
                ->count();
            $steps[] = [
                'label' => 'Tienen Email',
                'count' => $haveEmail,
                'pct'   => $totalApproved > 0 ? round(($haveEmail / $totalApproved) * 100, 1) : 0.0,
                'drop'  => $totalApproved > 0 ? round((1 - $haveEmail / $totalApproved) * 100, 1) : 0.0,
            ];

            $startedClaim = \App\Models\AnalyticsEvent::where('event_type', 'claim_started')->count();
            $steps[] = [
                'label' => 'Iniciaron Claim',
                'count' => $startedClaim,
                'pct'   => $totalApproved > 0 ? round(($startedClaim / $totalApproved) * 100, 1) : 0.0,
                'drop'  => $haveEmail > 0 ? round((1 - $startedClaim / $haveEmail) * 100, 1) : 0.0,
            ];

            $completedClaim = Restaurant::where('is_claimed', true)->count();
            $steps[] = [
                'label' => 'Completaron Claim',
                'count' => $completedClaim,
                'pct'   => $totalApproved > 0 ? round(($completedClaim / $totalApproved) * 100, 1) : 0.0,
                'drop'  => $startedClaim > 0 ? round((1 - $completedClaim / $startedClaim) * 100, 1) : 0.0,
            ];

            $paying = Restaurant::whereIn('subscription_tier', ['premium', 'elite'])
                ->where('subscription_status', 'active')
                ->count();
            $steps[] = [
                'label' => 'Pagando (Premium/Elite)',
                'count' => $paying,
                'pct'   => $totalApproved > 0 ? round(($paying / $totalApproved) * 100, 1) : 0.0,
                'drop'  => $completedClaim > 0 ? round((1 - $paying / $completedClaim) * 100, 1) : 0.0,
            ];

            return $steps;
        } catch (\Throwable $e) {
            return [];
        }
    }

    // ─── Abandonment ─────────────────────────────────────────────────────────

    private function computeAbandonByStep(): array
    {
        try {
            $claimStartedTotal  = \App\Models\AnalyticsEvent::where('event_type', 'claim_started')->count();
            $claimCompletedTotal = \App\Models\AnalyticsEvent::where('event_type', 'claim_completed')->count();
            $startedNotFinished = max(0, $claimStartedTotal - $claimCompletedTotal);

            $claimedFree = Restaurant::where('is_claimed', true)
                ->where(function ($q) {
                    $q->whereNull('subscription_tier')
                      ->orWhere('subscription_tier', 'free');
                })
                ->count();

            $paidCanceled = Restaurant::whereIn('subscription_tier', ['premium', 'elite'])
                ->where('subscription_status', 'canceled')
                ->count();

            return [
                'started_not_finished' => $startedNotFinished,
                'claimed_free'         => $claimedFree,
                'paid_canceled'        => $paidCanceled,
            ];
        } catch (\Throwable $e) {
            return [
                'started_not_finished' => 0,
                'claimed_free'         => 0,
                'paid_canceled'        => 0,
            ];
        }
    }

    // ─── Revenue Stats ────────────────────────────────────────────────────────

    private function computeRevenueStats(): array
    {
        try {
            $premiumCount = Restaurant::where('subscription_tier', 'premium')
                ->where('subscription_status', 'active')
                ->count();

            $eliteCount = Restaurant::where('subscription_tier', 'elite')
                ->where('subscription_status', 'active')
                ->count();

            $freeCount = Restaurant::where('is_claimed', true)
                ->where(function ($q) {
                    $q->whereNull('subscription_tier')
                      ->orWhere('subscription_tier', 'free');
                })
                ->count();

            $mrr = ($premiumCount * 39) + ($eliteCount * 79);
            $arr = $mrr * 12;

            $totalPaying   = $premiumCount + $eliteCount;
            $avgMonthly    = $totalPaying > 0 ? ($mrr / $totalPaying) : 0;
            $avgLtv        = $avgMonthly * 8;

            return [
                'mrr'           => $mrr,
                'arr'           => $arr,
                'avg_ltv'       => $avgLtv,
                'premium_count' => $premiumCount,
                'elite_count'   => $eliteCount,
                'free_count'    => $freeCount,
                'total_paying'  => $totalPaying,
            ];
        } catch (\Throwable $e) {
            return [
                'mrr'           => 0,
                'arr'           => 0,
                'avg_ltv'       => 0,
                'premium_count' => 0,
                'elite_count'   => 0,
                'free_count'    => 0,
                'total_paying'  => 0,
            ];
        }
    }

    // ─── Conversion by Country ────────────────────────────────────────────────

    private function computeConversionByCountry(): array
    {
        try {
            $countries = ['US', 'MX'];
            $result = [];

            foreach ($countries as $country) {
                $total = Restaurant::where('status', 'approved')
                    ->where('country', $country)
                    ->count();

                $claimed = Restaurant::where('is_claimed', true)
                    ->where('country', $country)
                    ->count();

                $paid = Restaurant::whereIn('subscription_tier', ['premium', 'elite'])
                    ->where('subscription_status', 'active')
                    ->where('country', $country)
                    ->count();

                $result[] = [
                    'country'     => $country,
                    'total'       => $total,
                    'claimed'     => $claimed,
                    'paid'        => $paid,
                    'claim_rate'  => $total > 0 ? round(($claimed / $total) * 100, 1) : 0.0,
                    'paid_rate'   => $claimed > 0 ? round(($paid / $claimed) * 100, 1) : 0.0,
                ];
            }

            return $result;
        } catch (\Throwable $e) {
            return [];
        }
    }

    // ─── Weekly Cohorts ───────────────────────────────────────────────────────

    private function computeCohorts(): array
    {
        try {
            $cohorts = [];
            $weeksBack = 8;

            for ($i = $weeksBack - 1; $i >= 0; $i--) {
                $weekStart = Carbon::now()->startOfWeek()->subWeeks($i);
                $weekEnd   = $weekStart->copy()->endOfWeek();
                $label     = $weekStart->format('d/m');

                $initiated = \App\Models\AnalyticsEvent::whereBetween('created_at', [$weekStart, $weekEnd])
                    ->where('event_type', 'claim_started')
                    ->count();

                $completed = Restaurant::whereBetween('claimed_at', [$weekStart, $weekEnd])
                    ->where('is_claimed', true)
                    ->count();

                $paid = Restaurant::whereIn('subscription_tier', ['premium', 'elite'])
                    ->where('subscription_status', 'active')
                    ->whereBetween('subscription_started_at', [$weekStart, $weekEnd])
                    ->count();

                $cohorts[] = [
                    'week'      => $label,
                    'initiated' => $initiated,
                    'completed' => $completed,
                    'paid'      => $paid,
                    'rate'      => $initiated > 0 ? round(($completed / $initiated) * 100, 1) : 0.0,
                ];
            }

            return $cohorts;
        } catch (\Throwable $e) {
            return [];
        }
    }

    // ─── Daily Signups ────────────────────────────────────────────────────────

    private function computeDailySignups(): array
    {
        try {
            $days = 30;
            $rows = Restaurant::where('is_claimed', true)
                ->whereNotNull('claimed_at')
                ->where('claimed_at', '>=', Carbon::now()->subDays($days))
                ->selectRaw('DATE(claimed_at) as day, COUNT(*) as total')
                ->groupByRaw('DATE(claimed_at)')
                ->orderBy('day')
                ->get()
                ->keyBy('day');

            $result = [];
            for ($i = $days - 1; $i >= 0; $i--) {
                $date  = Carbon::now()->subDays($i)->toDateString();
                $count = $rows->has($date) ? (int) $rows[$date]->total : 0;
                $result[] = [
                    'date'  => $date,
                    'label' => Carbon::parse($date)->format('d/m'),
                    'count' => $count,
                ];
            }

            $max = collect($result)->max('count');

            return collect($result)->map(function ($row) use ($max) {
                $row['pct'] = $max > 0 ? round(($row['count'] / $max) * 100) : 0;
                return $row;
            })->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }
}

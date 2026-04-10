<?php

namespace App\Filament\Widgets;

use App\Models\AnalyticsEvent;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class ClaimFunnelWidget extends Widget
{
    protected static bool $isLazy = true;

    protected static string $view = 'filament.widgets.claim-funnel-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 10;

    public function getViewData(): array
    {
        $since = now()->subDays(30);

        $eventTypes = [
            'claim_page_view',
            'claim_search',
            'claim_restaurant_selected',
            'claim_verification_started',
            'claim_verification_completed',
            'claim_completed',
        ];

        $counts = AnalyticsEvent::where('created_at', '>=', $since)
            ->whereIn('event_type', $eventTypes)
            ->select('event_type', DB::raw('COUNT(*) as total'))
            ->groupBy('event_type')
            ->pluck('total', 'event_type')
            ->toArray();

        $pageViews         = $counts['claim_page_view']              ?? 0;
        $searches          = $counts['claim_search']                 ?? 0;
        $selected          = $counts['claim_restaurant_selected']    ?? 0;
        $verifyStarted     = $counts['claim_verification_started']   ?? 0;
        $verifyCompleted   = $counts['claim_verification_completed'] ?? 0;
        $completed         = $counts['claim_completed']              ?? 0;

        // Also count premium upgrades separately
        $premiumUpgrades = AnalyticsEvent::where('created_at', '>=', $since)
            ->where('event_type', 'claim_upgrade_to_premium')
            ->count();

        $steps = [
            [
                'label'      => 'Visitaron /claim',
                'event'      => 'claim_page_view',
                'count'      => $pageViews,
                'pct_prev'   => 100,
                'pct_total'  => 100,
            ],
            [
                'label'      => 'Buscaron restaurante',
                'event'      => 'claim_search',
                'count'      => $searches,
                'pct_prev'   => $pageViews   > 0 ? round($searches        / $pageViews   * 100, 1) : 0,
                'pct_total'  => $pageViews   > 0 ? round($searches        / $pageViews   * 100, 1) : 0,
            ],
            [
                'label'      => 'Seleccionaron restaurante',
                'event'      => 'claim_restaurant_selected',
                'count'      => $selected,
                'pct_prev'   => $searches    > 0 ? round($selected        / $searches    * 100, 1) : 0,
                'pct_total'  => $pageViews   > 0 ? round($selected        / $pageViews   * 100, 1) : 0,
            ],
            [
                'label'      => 'Iniciaron verificación',
                'event'      => 'claim_verification_started',
                'count'      => $verifyStarted,
                'pct_prev'   => $selected    > 0 ? round($verifyStarted   / $selected    * 100, 1) : 0,
                'pct_total'  => $pageViews   > 0 ? round($verifyStarted   / $pageViews   * 100, 1) : 0,
            ],
            [
                'label'      => 'Completaron verificación',
                'event'      => 'claim_verification_completed',
                'count'      => $verifyCompleted,
                'pct_prev'   => $verifyStarted > 0 ? round($verifyCompleted / $verifyStarted * 100, 1) : 0,
                'pct_total'  => $pageViews     > 0 ? round($verifyCompleted / $pageViews     * 100, 1) : 0,
            ],
            [
                'label'      => 'Claim completado',
                'event'      => 'claim_completed',
                'count'      => $completed,
                'pct_prev'   => $verifyCompleted > 0 ? round($completed / $verifyCompleted * 100, 1) : 0,
                'pct_total'  => $pageViews       > 0 ? round($completed / $pageViews       * 100, 1) : 0,
            ],
        ];

        $conversionRate = $pageViews > 0 ? round($completed / $pageViews * 100, 2) : 0;

        return [
            'steps'          => $steps,
            'conversionRate' => $conversionRate,
            'premiumUpgrades'=> $premiumUpgrades,
            'pageViews'      => $pageViews,
            'completed'      => $completed,
            'period'         => 'últimos 30 días',
        ];
    }
}

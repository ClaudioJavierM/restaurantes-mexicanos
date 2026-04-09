<?php

namespace App\Filament\Widgets;

use App\Models\Restaurant;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriptionFunnelWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = true;

    protected static ?string $pollingInterval = '60s';

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        try {
            $totalApproved = Restaurant::where('status', 'approved')->count();
            $totalClaimed  = Restaurant::where('is_claimed', true)->count();
            $withSub       = Restaurant::whereNotNull('subscription_tier')->count();
            $premium       = Restaurant::where('subscription_tier', 'premium')
                                ->where('subscription_status', 'active')
                                ->count();
            $elite         = Restaurant::where('subscription_tier', 'elite')
                                ->where('subscription_status', 'active')
                                ->count();

            $claimRate   = $totalApproved > 0 ? round(($totalClaimed / $totalApproved) * 100, 1) : 0;
            $subRate     = $totalClaimed > 0  ? round(($withSub / $totalClaimed) * 100, 1) : 0;
            $mrr         = ($premium * 29) + ($elite * 79);
            $mrrFormatted = '$' . number_format($mrr) . ' USD';

            return [
                Stat::make('Total Aprobados', number_format($totalApproved))
                    ->description('Restaurantes en el directorio')
                    ->descriptionIcon('heroicon-m-building-storefront')
                    ->color('gray'),

                Stat::make('Reclamados', number_format($totalClaimed))
                    ->description($claimRate . '% tasa de claim')
                    ->descriptionIcon('heroicon-m-flag')
                    ->color('info'),

                Stat::make('Con Suscripción', number_format($withSub))
                    ->description($subRate . '% de reclamados')
                    ->descriptionIcon('heroicon-m-credit-card')
                    ->color('warning'),

                Stat::make('Premium ⭐ Activos', number_format($premium))
                    ->description('$29/mes · $' . number_format($premium * 29) . ' MRR')
                    ->descriptionIcon('heroicon-m-star')
                    ->color('warning'),

                Stat::make('Elite 👑 Activos', number_format($elite))
                    ->description('$79/mes · $' . number_format($elite * 79) . ' MRR')
                    ->descriptionIcon('heroicon-m-trophy')
                    ->color('purple'),

                Stat::make('MRR Total', $mrrFormatted)
                    ->description('ARR: $' . number_format($mrr * 12) . ' USD')
                    ->descriptionIcon('heroicon-m-currency-dollar')
                    ->color('success'),
            ];
        } catch (\Throwable $e) {
            return [
                Stat::make('Error', 'Sin datos')
                    ->description('No se pudo cargar el funnel')
                    ->color('danger'),
            ];
        }
    }
}

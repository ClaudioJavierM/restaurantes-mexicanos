<?php

namespace App\Filament\Widgets;

use App\Models\Restaurant;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class RevenueOverviewWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = true;

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        try {
            $premiumCount = Restaurant::where('subscription_tier', 'premium')
                ->where('subscription_status', 'active')
                ->count();
        } catch (\Throwable $e) {
            $premiumCount = 0;
        }

        try {
            $eliteCount = Restaurant::where('subscription_tier', 'elite')
                ->where('subscription_status', 'active')
                ->count();
        } catch (\Throwable $e) {
            $eliteCount = 0;
        }

        $premiumRevenue = $premiumCount * 29;
        $eliteRevenue = $eliteCount * 79;
        $mrr = $premiumRevenue + $eliteRevenue;
        $arr = $mrr * 12;

        try {
            $expiresThisMonth = Restaurant::whereNotNull('subscription_expires_at')
                ->whereBetween('subscription_expires_at', [now(), now()->endOfMonth()])
                ->count();
        } catch (\Throwable $e) {
            $expiresThisMonth = 0;
        }

        try {
            $approvedTotal = Restaurant::where('status', 'approved')->count();
            $claimedCount = Restaurant::where('is_claimed', true)->count();
            $conversionRate = $approvedTotal > 0 ? round(($claimedCount / $approvedTotal) * 100, 1) : 0;
        } catch (\Throwable $e) {
            $approvedTotal = 0;
            $claimedCount = 0;
            $conversionRate = 0;
        }

        return [
            Stat::make('MRR Estimado', '$' . number_format($mrr) . ' USD')
                ->description('Ingresos mensuales recurrentes')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Plan Premium', number_format($premiumCount))
                ->description('$29/mes × ' . $premiumCount . ' = $' . number_format($premiumRevenue))
                ->descriptionIcon('heroicon-m-star')
                ->color('primary'),

            Stat::make('Plan Elite', number_format($eliteCount))
                ->description('$79/mes × ' . $eliteCount . ' = $' . number_format($eliteRevenue))
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning'),

            Stat::make('Vencen Este Mes', number_format($expiresThisMonth))
                ->description('Suscripciones por renovar')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('Tasa de Conversión', $conversionRate . '%')
                ->description('Unclaimed → Claimed (' . number_format($claimedCount) . ' / ' . number_format($approvedTotal) . ')')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),

            Stat::make('ARR Estimado', '$' . number_format($arr) . ' USD')
                ->description('Proyección anual (MRR × 12)')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),
        ];
    }
}

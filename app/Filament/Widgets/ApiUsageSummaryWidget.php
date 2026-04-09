<?php

namespace App\Filament\Widgets;

use App\Models\ApiCallLog;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ApiUsageSummaryWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = true;

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $pollingInterval = null;

    protected ?string $heading = 'Uso de APIs';

    protected ?string $description = 'Consumo y presupuesto de APIs externas este mes';

    // Total Yelp quota: 7 keys × 5,000 calls/month
    protected const YELP_MONTHLY_QUOTA = 35000;

    // Number of Yelp premium keys (approximate static config)
    protected const YELP_PREMIUM_KEYS = 2;

    protected function getStats(): array
    {
        $startOfMonth = now()->startOfMonth();

        // --- Yelp calls this month ---
        try {
            $yelpUsed = ApiCallLog::where('service', 'yelp')
                ->where('called_at', '>=', $startOfMonth)
                ->count();
        } catch (\Throwable $e) {
            $yelpUsed = 0;
        }

        $yelpQuota = self::YELP_MONTHLY_QUOTA;
        $yelpRemaining = max(0, $yelpQuota - $yelpUsed);
        $yelpUsagePct = $yelpQuota > 0 ? round(($yelpUsed / $yelpQuota) * 100, 1) : 0;
        $yelpColor = $yelpUsagePct >= 85 ? 'danger' : ($yelpUsagePct >= 60 ? 'warning' : 'success');

        // Daily average for remaining days estimate
        $dayOfMonth = now()->day;
        $dailyAvg = $dayOfMonth > 0 ? ($yelpUsed / $dayOfMonth) : 1;
        $daysRemaining = $dailyAvg > 0 ? round($yelpRemaining / $dailyAvg) : 999;

        // --- Google calls this month ---
        try {
            $googleUsed = ApiCallLog::where('service', 'google')
                ->where('called_at', '>=', $startOfMonth)
                ->count();
        } catch (\Throwable $e) {
            $googleUsed = 0;
        }

        // --- Yelp success rate ---
        try {
            $yelpSuccess = ApiCallLog::where('service', 'yelp')
                ->where('called_at', '>=', $startOfMonth)
                ->where('success', true)
                ->count();
        } catch (\Throwable $e) {
            $yelpSuccess = 0;
        }

        $successRate = $yelpUsed > 0 ? round(($yelpSuccess / $yelpUsed) * 100, 1) : 100;
        $successColor = $successRate >= 95 ? 'success' : ($successRate >= 85 ? 'warning' : 'danger');

        // --- Google API cost this month ---
        try {
            $googleCost = ApiCallLog::where('service', 'google')
                ->where('called_at', '>=', $startOfMonth)
                ->sum('cost');
        } catch (\Throwable $e) {
            $googleCost = 0;
        }

        return [
            Stat::make('Llamadas Yelp Este Mes', number_format($yelpUsed))
                ->description('de ' . number_format($yelpQuota) . ' disponibles (' . $yelpUsagePct . '% usado)')
                ->descriptionIcon('heroicon-m-fire')
                ->color($yelpColor),

            Stat::make('Presupuesto Restante', number_format($yelpRemaining))
                ->description('~' . $daysRemaining . ' días restantes al ritmo actual')
                ->descriptionIcon('heroicon-m-battery-100')
                ->color($yelpColor),

            Stat::make('Keys Premium Activas', self::YELP_PREMIUM_KEYS)
                ->description('12 fotos + atributos completos')
                ->descriptionIcon('heroicon-m-key')
                ->color('warning'),

            Stat::make('Llamadas Google', number_format($googleUsed))
                ->description('Google Places API este mes')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('primary'),

            Stat::make('Tasa de Éxito Yelp', $successRate . '%')
                ->description($yelpSuccess . ' exitosas de ' . number_format($yelpUsed) . ' llamadas')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($successColor),

            Stat::make('Costo Google Este Mes', '$' . number_format((float) $googleCost, 2) . ' USD')
                ->description('Basado en cost por llamada')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('info'),
        ];
    }
}

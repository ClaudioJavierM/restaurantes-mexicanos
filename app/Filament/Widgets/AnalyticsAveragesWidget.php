<?php

namespace App\Filament\Widgets;

use App\Models\AnalyticsEvent;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsAveragesWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected ?string $heading = "Estadísticas de Visitas";
    protected ?string $description = "Promedios de tráfico del sitio";
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $user = auth()->user();

        // Only show to admins
        if (!$user || !$user->isAdmin()) {
            return [];
        }

        // Get first and last record dates
        $firstRecord = AnalyticsEvent::orderBy('created_at', 'asc')->first();
        $lastRecord = AnalyticsEvent::orderBy('created_at', 'desc')->first();

        if (!$firstRecord) {
            return [
                Stat::make('Sin Datos', 'No hay registros de analytics')
                    ->color('warning'),
            ];
        }

        $firstDate = Carbon::parse($firstRecord->created_at);
        $lastDate = Carbon::parse($lastRecord->created_at);
        $totalDays = max(1, $firstDate->diffInDays($lastDate) + 1);
        $totalWeeks = max(1, $firstDate->diffInWeeks($lastDate) + 1);
        $totalMonths = max(1, $firstDate->diffInMonths($lastDate) + 1);

        // Total page views
        $totalViews = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)->count();

        // Calculate averages
        $avgPerDay = $totalViews / $totalDays;
        $avgPerWeek = $totalViews / $totalWeeks;
        $avgPerMonth = $totalViews / $totalMonths;
        $projectedYearly = $avgPerDay * 365;

        // Get today's views
        $todayViews = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
            ->whereDate('created_at', Carbon::today())
            ->count();

        // Get this week's views
        $thisWeekViews = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        // Get this month's views
        $thisMonthViews = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->count();

        // Compare today vs daily average
        $todayVsAvg = $avgPerDay > 0 ? (($todayViews - $avgPerDay) / $avgPerDay) * 100 : 0;

        // Get peak day
        $peakDay = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderByDesc('count')
            ->first();

        // Get daily data for chart (last 30 days)
        $dailyChart = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();

        return [
            Stat::make('Promedio Diario', number_format($avgPerDay, 0))
                ->description('Hoy: ' . number_format($todayViews) . ' (' . ($todayVsAvg >= 0 ? '+' : '') . number_format($todayVsAvg, 1) . '%)')
                ->descriptionIcon($todayVsAvg >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($todayVsAvg >= 0 ? 'success' : 'warning')
                ->chart($dailyChart),

            Stat::make('Promedio Semanal', number_format($avgPerWeek, 0))
                ->description('Esta semana: ' . number_format($thisWeekViews))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Promedio Mensual', number_format($avgPerMonth, 0))
                ->description('Este mes: ' . number_format($thisMonthViews))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),

            Stat::make('Proyeccion Anual', number_format($projectedYearly, 0))
                ->description('Basado en ' . $totalDays . ' dias de datos')
                ->descriptionIcon('heroicon-m-rocket-launch')
                ->color('success'),

            Stat::make('Dia Pico', number_format($peakDay->count ?? 0))
                ->description($peakDay ? Carbon::parse($peakDay->date)->format('d M Y') : 'N/A')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning'),

            Stat::make('Total Historico', number_format($totalViews))
                ->description('Desde ' . $firstDate->format('d M Y'))
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('gray'),
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && $user->isAdmin();
    }
}

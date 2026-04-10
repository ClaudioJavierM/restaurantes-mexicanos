<?php

namespace App\Filament\Owner\Widgets;

use App\Models\Restaurant;
use App\Models\AnalyticsEvent;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $restaurant = auth()->user()->firstAccessibleRestaurant();

        if (!$restaurant) {
            return [
                Stat::make('Sin Restaurante', 'Reclama tu restaurante para ver estadísticas')
                    ->description('Ve a la sección de Claim para reclamar tu restaurante')
                    ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->color('warning'),
            ];
        }

        // Get real analytics data from analytics_events table
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        // Current period (last 30 days)
        $profileViews = AnalyticsEvent::where('restaurant_id', $restaurant->id)
            ->where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $phoneClicks = AnalyticsEvent::where('restaurant_id', $restaurant->id)
            ->where('event_type', AnalyticsEvent::EVENT_PHONE_CLICK)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $websiteClicks = AnalyticsEvent::where('restaurant_id', $restaurant->id)
            ->where('event_type', AnalyticsEvent::EVENT_WEBSITE_CLICK)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $directionClicks = AnalyticsEvent::where('restaurant_id', $restaurant->id)
            ->where('event_type', AnalyticsEvent::EVENT_DIRECTION_CLICK)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Previous period for comparison (30-60 days ago)
        $prevStartDate = Carbon::now()->subDays(60);
        $prevEndDate = Carbon::now()->subDays(30);

        $prevProfileViews = AnalyticsEvent::where('restaurant_id', $restaurant->id)
            ->where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->count();

        // Calculate trend
        $viewsTrend = $prevProfileViews > 0 
            ? (($profileViews - $prevProfileViews) / $prevProfileViews) * 100 
            : ($profileViews > 0 ? 100 : 0);

        // Get chart data for last 7 days
        $chartData = $this->getChartData($restaurant->id, AnalyticsEvent::EVENT_PAGE_VIEW);

        // Unique visitors
        $uniqueVisitors = AnalyticsEvent::where('restaurant_id', $restaurant->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->distinct('session_id')
            ->count('session_id');

        // Total interactions (leads)
        $totalLeads = $phoneClicks + $websiteClicks + $directionClicks;
        $conversionRate = $profileViews > 0 ? ($totalLeads / $profileViews) * 100 : 0;

        return [
            Stat::make('Vistas del Perfil', number_format($profileViews))
                ->description($this->getTrendDescription($viewsTrend))
                ->descriptionIcon($viewsTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($viewsTrend >= 0 ? 'success' : 'danger')
                ->chart($chartData),

            Stat::make('Visitantes Únicos', number_format($uniqueVisitors))
                ->description('Últimos 30 días')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Clicks al Teléfono', number_format($phoneClicks))
                ->description('Llamadas potenciales')
                ->descriptionIcon('heroicon-m-phone')
                ->color('warning'),

            Stat::make('Clicks al Sitio Web', number_format($websiteClicks))
                ->description('Visitas a tu website')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('primary'),

            Stat::make('Solicitudes de Dirección', number_format($directionClicks))
                ->description('Abrieron GPS/Mapa')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('success'),

            Stat::make('Tasa de Conversión', number_format($conversionRate, 1) . '%')
                ->description(number_format($totalLeads) . ' leads de ' . number_format($profileViews) . ' vistas')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($conversionRate >= 5 ? 'success' : ($conversionRate >= 2 ? 'warning' : 'danger')),
        ];
    }

    protected function getTrendDescription(float $percentage): string
    {
        $abs = abs($percentage);
        $formatted = number_format($abs, 1);

        if ($percentage > 0) {
            return "+{$formatted}% vs mes anterior";
        } elseif ($percentage < 0) {
            return "-{$formatted}% vs mes anterior";
        }

        return 'Sin cambios vs mes anterior';
    }

    protected function getChartData(int $restaurantId, string $eventType): array
    {
        $data = AnalyticsEvent::where('restaurant_id', $restaurantId)
            ->where('event_type', $eventType)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();

        // Ensure we have 7 data points
        while (count($data) < 7) {
            array_unshift($data, 0);
        }

        return array_slice($data, -7);
    }
}

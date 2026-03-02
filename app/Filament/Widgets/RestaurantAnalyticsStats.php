<?php

namespace App\Filament\Widgets;

use App\Models\AnalyticsEvent;
use App\Models\Restaurant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class RestaurantAnalyticsStats extends BaseWidget
{
    public ?string $filter = '30'; // Default to last 30 days

    protected function getFilters(): ?array
    {
        return [
            '7' => 'Últimos 7 días',
            '30' => 'Últimos 30 días',
            '90' => 'Últimos 90 días',
            '365' => 'Último año',
        ];
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $days = (int) $this->filter;
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        // If user is admin, show system-wide stats
        if ($user && $user->isAdmin()) {
            return $this->getAdminStats($startDate, $endDate, $days);
        }

        // If user is owner, show only their restaurants' stats
        if ($user && $user->isOwner()) {
            return $this->getOwnerStats($user->id, $startDate, $endDate, $days);
        }

        return [];
    }

    protected function getAdminStats(Carbon $startDate, Carbon $endDate, int $days): array
    {
        $totalViews = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $phoneClicks = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PHONE_CLICK)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $websiteClicks = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_WEBSITE_CLICK)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $directionClicks = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_DIRECTION_CLICK)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $couponClicks = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_COUPON_CLICK)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Get trend data for previous period
        $prevStartDate = Carbon::now()->subDays($days * 2)->startOfDay();
        $prevEndDate = $startDate->copy()->endOfDay();

        $prevViews = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->count();

        $viewsTrend = $prevViews > 0 ? (($totalViews - $prevViews) / $prevViews) * 100 : 0;

        return [
            Stat::make('Vistas Totales', number_format($totalViews))
                ->description($this->getTrendDescription($viewsTrend))
                ->descriptionIcon($viewsTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($viewsTrend >= 0 ? 'success' : 'danger')
                ->chart($this->getChartData(AnalyticsEvent::EVENT_PAGE_VIEW, $startDate, $endDate)),

            Stat::make('Clicks en Teléfono', number_format($phoneClicks))
                ->description('Llamadas generadas')
                ->descriptionIcon('heroicon-m-phone')
                ->color('info'),

            Stat::make('Clicks en Sitio Web', number_format($websiteClicks))
                ->description('Visitas al website')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('warning'),

            Stat::make('Solicitudes de Dirección', number_format($directionClicks))
                ->description('GPS/Mapa abierto')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('success'),

            Stat::make('Clicks en Cupones', number_format($couponClicks))
                ->description('Cupones utilizados')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary'),
        ];
    }

    protected function getOwnerStats(int $userId, Carbon $startDate, Carbon $endDate, int $days): array
    {
        $restaurantIds = Restaurant::where('user_id', $userId)->pluck('id');

        if ($restaurantIds->isEmpty()) {
            return [
                Stat::make('Sin Restaurantes', '0')
                    ->description('No tienes restaurantes registrados')
                    ->color('warning'),
            ];
        }

        $totalViews = AnalyticsEvent::whereIn('restaurant_id', $restaurantIds)
            ->where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $phoneClicks = AnalyticsEvent::whereIn('restaurant_id', $restaurantIds)
            ->where('event_type', AnalyticsEvent::EVENT_PHONE_CLICK)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $websiteClicks = AnalyticsEvent::whereIn('restaurant_id', $restaurantIds)
            ->where('event_type', AnalyticsEvent::EVENT_WEBSITE_CLICK)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $directionClicks = AnalyticsEvent::whereIn('restaurant_id', $restaurantIds)
            ->where('event_type', AnalyticsEvent::EVENT_DIRECTION_CLICK)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $couponViews = AnalyticsEvent::whereIn('restaurant_id', $restaurantIds)
            ->where('event_type', AnalyticsEvent::EVENT_COUPON_VIEW)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $couponClicks = AnalyticsEvent::whereIn('restaurant_id', $restaurantIds)
            ->where('event_type', AnalyticsEvent::EVENT_COUPON_CLICK)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $uniqueVisitors = AnalyticsEvent::whereIn('restaurant_id', $restaurantIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->distinct('session_id')
            ->count('session_id');

        // Calculate conversion rate (actions / views)
        $conversionRate = $totalViews > 0
            ? (($phoneClicks + $websiteClicks + $directionClicks) / $totalViews) * 100
            : 0;

        // Coupon effectiveness
        $couponEffectiveness = $couponViews > 0
            ? ($couponClicks / $couponViews) * 100
            : 0;

        return [
            Stat::make('Vistas de Perfil', number_format($totalViews))
                ->description("$uniqueVisitors visitantes únicos")
                ->descriptionIcon('heroicon-m-eye')
                ->color('primary')
                ->chart($this->getOwnerChartData($restaurantIds, AnalyticsEvent::EVENT_PAGE_VIEW, $startDate, $endDate)),

            Stat::make('Customer Leads', number_format($phoneClicks + $websiteClicks + $directionClicks))
                ->description(number_format($conversionRate, 1) . '% tasa de conversión')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('Llamadas Telefónicas', number_format($phoneClicks))
                ->description('Clicks en teléfono')
                ->descriptionIcon('heroicon-m-phone')
                ->color('info'),

            Stat::make('Visitas al Website', number_format($websiteClicks))
                ->description('Clicks en sitio web')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('warning'),

            Stat::make('Solicitudes de Dirección', number_format($directionClicks))
                ->description('Abrieron GPS/Mapa')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('success'),

            Stat::make('Engagement de Cupones', number_format($couponClicks) . ' / ' . number_format($couponViews))
                ->description(number_format($couponEffectiveness, 1) . '% efectividad')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary'),
        ];
    }

    protected function getTrendDescription(float $percentage): string
    {
        $abs = abs($percentage);
        $formatted = number_format($abs, 1);

        if ($percentage > 0) {
            return "+{$formatted}% vs periodo anterior";
        } elseif ($percentage < 0) {
            return "-{$formatted}% vs periodo anterior";
        }

        return 'Sin cambios';
    }

    protected function getChartData(string $eventType, Carbon $startDate, Carbon $endDate): array
    {
        $dailyData = AnalyticsEvent::where('event_type', $eventType)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();

        return array_slice($dailyData, -7); // Last 7 days for chart
    }

    protected function getOwnerChartData($restaurantIds, string $eventType, Carbon $startDate, Carbon $endDate): array
    {
        $dailyData = AnalyticsEvent::whereIn('restaurant_id', $restaurantIds)
            ->where('event_type', $eventType)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();

        return array_slice($dailyData, -7); // Last 7 days for chart
    }
}

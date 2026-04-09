<?php

namespace App\Filament\Widgets;

use App\Models\Restaurant;
use App\Models\Review;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class OverviewStatsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = true;

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        try {
            $approvedTotal = Restaurant::where('status', 'approved')->count();
        } catch (\Throwable $e) {
            $approvedTotal = 0;
        }

        try {
            $approvedLastWeek = Restaurant::where('status', 'approved')
                ->where('created_at', '<', now()->subDays(7))
                ->count();
            $newThisWeek = $approvedTotal - $approvedLastWeek;
        } catch (\Throwable $e) {
            $newThisWeek = 0;
        }

        try {
            $claimedCount = Restaurant::where('is_claimed', true)->count();
            $claimedPct = $approvedTotal > 0 ? round(($claimedCount / $approvedTotal) * 100, 1) : 0;
        } catch (\Throwable $e) {
            $claimedCount = 0;
            $claimedPct = 0;
        }

        try {
            $activeSubscribers = Restaurant::whereNotNull('subscription_tier')
                ->where('subscription_status', 'active')
                ->count();
        } catch (\Throwable $e) {
            $activeSubscribers = 0;
        }

        try {
            $reviewsThisWeek = Review::where('created_at', '>=', now()->subDays(7))->count();
        } catch (\Throwable $e) {
            $reviewsThisWeek = 0;
        }

        try {
            $newToday = Restaurant::whereDate('created_at', today())->count();
        } catch (\Throwable $e) {
            $newToday = 0;
        }

        try {
            $pendingApproval = Restaurant::where('status', 'pending')->count();
        } catch (\Throwable $e) {
            $pendingApproval = 0;
        }

        try {
            $approvedTrend = $this->getDailyTrend('restaurants', 'created_at', 7, ['status' => 'approved']);
        } catch (\Throwable $e) {
            $approvedTrend = [0, 0, 0, 0, 0, 0, 0];
        }

        return [
            Stat::make('Total Restaurantes', number_format($approvedTotal))
                ->description('+' . number_format($newThisWeek) . ' esta semana')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('success')
                ->chart($approvedTrend),

            Stat::make('Reclamados', number_format($claimedCount))
                ->description($claimedPct . '% del total')
                ->descriptionIcon('heroicon-m-key')
                ->color('primary'),

            Stat::make('Suscriptores Activos', number_format($activeSubscribers))
                ->description('Premium + Elite')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            Stat::make('Reviews Esta Semana', number_format($reviewsThisWeek))
                ->description('Últimos 7 días')
                ->descriptionIcon('heroicon-m-chat-bubble-left-ellipsis')
                ->color('info'),

            Stat::make('Nuevos Hoy', number_format($newToday))
                ->description('Importados / registrados hoy')
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('success'),

            Stat::make('Pendientes Aprobación', number_format($pendingApproval))
                ->description('Por revisar y aprobar')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }

    /**
     * Return daily counts for the last N days as a sparkline array.
     */
    protected function getDailyTrend(string $table, string $dateCol, int $days, array $where = []): array
    {
        $trend = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $query = DB::table($table)->whereDate($dateCol, $date);
            foreach ($where as $col => $val) {
                $query->where($col, $val);
            }
            $trend[] = $query->count();
        }
        return $trend;
    }
}

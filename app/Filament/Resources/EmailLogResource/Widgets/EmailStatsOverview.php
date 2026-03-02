<?php

namespace App\Filament\Resources\EmailLogResource\Widgets;

use App\Models\EmailLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class EmailStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        // Today's stats
        $todayTotal = EmailLog::whereDate('sent_at', $today)->count();
        $todayFailed = EmailLog::whereDate('sent_at', $today)->whereIn('status', ['failed', 'bounced'])->count();

        // This week stats
        $weekTotal = EmailLog::where('sent_at', '>=', $thisWeek)->count();

        // This month stats
        $monthTotal = EmailLog::where('sent_at', '>=', $thisMonth)->count();
        $monthOpened = EmailLog::where('sent_at', '>=', $thisMonth)->whereNotNull('opened_at')->count();
        $monthClicked = EmailLog::where('sent_at', '>=', $thisMonth)->whereNotNull('first_clicked_at')->count();

        // Calculate open rate
        $totalSent = EmailLog::whereIn('status', ['sent', 'delivered', 'opened', 'clicked'])->count();
        $totalOpened = EmailLog::whereNotNull('opened_at')->count();
        $openRate = $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 1) : 0;

        // Calculate click rate
        $totalClicked = EmailLog::whereNotNull('clicked_at')->count();
        $clickRate = $totalOpened > 0 ? round(($totalClicked / $totalOpened) * 100, 1) : 0;

        return [
            Stat::make('Emails Hoy', number_format($todayTotal))
                ->description($todayFailed > 0 ? "{$todayFailed} fallidos" : 'Todos enviados')
                ->descriptionIcon($todayFailed > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($todayFailed > 0 ? 'danger' : 'success'),

            Stat::make('Esta Semana', number_format($weekTotal))
                ->description('Emails enviados')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('info'),

            Stat::make('Tasa de Apertura', "{$openRate}%")
                ->description("{$totalOpened} de {$totalSent} emails")
                ->descriptionIcon('heroicon-m-eye')
                ->color($openRate >= 20 ? 'success' : ($openRate >= 10 ? 'warning' : 'danger')),

            Stat::make('Tasa de Clicks', "{$clickRate}%")
                ->description("{$totalClicked} clicks de {$totalOpened} abiertos")
                ->descriptionIcon('heroicon-m-cursor-arrow-rays')
                ->color($clickRate >= 5 ? 'success' : ($clickRate >= 2 ? 'warning' : 'gray')),
        ];
    }
}

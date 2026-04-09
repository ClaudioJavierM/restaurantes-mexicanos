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

        // Only count FAMER emails (exclude SDV/external via Resend webhooks)
        $famer = fn() => EmailLog::whereNotNull('from_email');

        // Today's stats
        $todayTotal = $famer()->whereDate('sent_at', $today)->count();
        $todayFailed = $famer()->whereDate('sent_at', $today)->whereIn('status', ['failed', 'bounced'])->count();

        // This week stats
        $weekTotal = $famer()->where('sent_at', '>=', $thisWeek)->count();

        // This month stats
        $monthTotal = $famer()->where('sent_at', '>=', $thisMonth)->count();
        $monthOpened = $famer()->where('sent_at', '>=', $thisMonth)->whereNotNull('opened_at')->count();
        $monthClicked = $famer()->where('sent_at', '>=', $thisMonth)->whereNotNull('clicked_at')->count();

        // Calculate open rate
        $totalSent = $famer()->whereIn('status', ['sent', 'delivered', 'opened', 'clicked'])->count();
        $totalOpened = $famer()->whereNotNull('opened_at')->count();
        $openRate = $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 1) : 0;

        // Calculate click rate
        $totalClicked = $famer()->whereNotNull('clicked_at')->count();
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

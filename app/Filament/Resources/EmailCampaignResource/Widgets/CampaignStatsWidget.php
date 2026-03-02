<?php

namespace App\Filament\Resources\EmailCampaignResource\Widgets;

use App\Models\EmailCampaign;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CampaignStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalCampaigns = EmailCampaign::count();
        $activeCampaigns = EmailCampaign::whereIn('status', ['scheduled', 'sending'])->count();
        $completedCampaigns = EmailCampaign::where('status', 'sent')->count();

        // Get total stats from completed campaigns
        $stats = EmailCampaign::where('status', 'sent')
            ->selectRaw('
                SUM(sent_count) as total_sent,
                SUM(opened_count) as total_opened,
                SUM(clicked_count) as total_clicked,
                SUM(bounced_count) as total_bounced
            ')
            ->first();

        $totalSent = $stats->total_sent ?? 0;
        $totalOpened = $stats->total_opened ?? 0;
        $totalClicked = $stats->total_clicked ?? 0;

        $avgOpenRate = $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 1) : 0;
        $avgClickRate = $totalSent > 0 ? round(($totalClicked / $totalSent) * 100, 1) : 0;

        return [
            Stat::make('Total Campañas', $totalCampaigns)
                ->description("{$completedCampaigns} completadas")
                ->descriptionIcon('heroicon-m-megaphone')
                ->color('primary'),

            Stat::make('Campañas Activas', $activeCampaigns)
                ->description($activeCampaigns > 0 ? 'En envío o programadas' : 'Ninguna activa')
                ->descriptionIcon($activeCampaigns > 0 ? 'heroicon-m-arrow-path' : 'heroicon-m-pause')
                ->color($activeCampaigns > 0 ? 'warning' : 'gray'),

            Stat::make('Emails Enviados', number_format($totalSent))
                ->description('Total de todas las campañas')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('info'),

            Stat::make('Tasa Promedio de Apertura', "{$avgOpenRate}%")
                ->description("{$totalOpened} aperturas totales")
                ->descriptionIcon('heroicon-m-eye')
                ->color($avgOpenRate >= 20 ? 'success' : ($avgOpenRate >= 10 ? 'warning' : 'danger')),

            Stat::make('Tasa Promedio de Clicks', "{$avgClickRate}%")
                ->description("{$totalClicked} clicks totales")
                ->descriptionIcon('heroicon-m-cursor-arrow-rays')
                ->color($avgClickRate >= 3 ? 'success' : ($avgClickRate >= 1 ? 'warning' : 'gray')),
        ];
    }
}

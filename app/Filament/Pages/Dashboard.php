<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ApiUsageSummaryWidget;
use App\Filament\Widgets\DataCompletenessWidget;
use App\Filament\Widgets\OverviewStatsWidget;
use App\Filament\Widgets\RestaurantGrowthWidget;
use App\Filament\Widgets\RevenueOverviewWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Overview';

    protected static ?string $title = 'Command Center';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = null;

    public function getWidgets(): array
    {
        return [
            OverviewStatsWidget::class,
            RevenueOverviewWidget::class,
            RestaurantGrowthWidget::class,
            DataCompletenessWidget::class,
            ApiUsageSummaryWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 4;
    }
}

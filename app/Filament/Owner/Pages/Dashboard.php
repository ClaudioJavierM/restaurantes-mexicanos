<?php

namespace App\Filament\Owner\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.owner.pages.dashboard';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Owner\Widgets\ProfileCompletenessWidget::class,
            \App\Filament\Owner\Widgets\StatsOverview::class,
            \App\Filament\Owner\Widgets\RecentReviewsWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 2;
    }
}

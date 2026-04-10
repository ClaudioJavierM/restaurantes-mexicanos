<?php

namespace App\Filament\Owner\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.owner.pages.dashboard';

    public function mount(): void
    {
        $restaurant = Restaurant::where('user_id', Auth::id())->first();
        if ($restaurant && !$restaurant->onboarding_completed) {
            $this->redirect(OnboardingPage::getUrl());
        }
    }

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

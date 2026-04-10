<?php

namespace App\Filament\Owner\Pages;

use App\Models\Restaurant;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class OnboardingPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';
    protected static ?string $navigationLabel = 'Configuración inicial';
    protected static ?string $title = 'Configura tu restaurante';
    protected static ?string $slug = 'onboarding';

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.owner.pages.onboarding';

    public int $restaurantId;

    public function mount(): void
    {
        $user = Auth::user();

        /** @var Restaurant|null $restaurant */
        $restaurant = $user->allAccessibleRestaurants()->first();

        if (!$restaurant) {
            $this->redirect(filament()->getHomeUrl());
            return;
        }

        // Already completed — go to dashboard
        if ($restaurant->onboarding_completed) {
            $this->redirect(filament()->getPanel('owner')->getUrl());
            return;
        }

        $this->restaurantId = $restaurant->id;
    }

    public function getTitle(): string
    {
        return 'Configura tu restaurante';
    }
}

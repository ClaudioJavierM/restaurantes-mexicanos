<?php

namespace App\Filament\Owner\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class LoyaltyProgram extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationLabel = 'Programa de Lealtad';
    protected static ?string $title = 'Programa de Lealtad';
    protected static ?string $navigationGroup = 'Mi Negocio';
    protected static ?int $navigationSort = 20;

    protected static string $view = 'filament.owner.pages.loyalty-program';

    public $restaurant = null;

    public function mount(): void
    {
        $this->restaurant = Auth::user()?->firstAccessibleRestaurant();
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        $restaurant = $user->firstAccessibleRestaurant();
        return $restaurant && in_array($restaurant->subscription_tier, ['premium', 'elite']);
    }
}

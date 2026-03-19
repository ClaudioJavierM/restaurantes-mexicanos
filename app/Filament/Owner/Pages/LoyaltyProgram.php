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
    
    protected static string $view = 'filament.owner.pages.coming-soon';

    public string $featureName = 'Programa de Lealtad';
    public string $featureDescription = 'Muy pronto podras crear un programa de lealtad para recompensar a tus clientes mas fieles. Puntos por visita, recompensas personalizadas y mas.';

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        $restaurant = $user->allAccessibleRestaurants()->first();
        return $restaurant && $restaurant->is_claimed;
    }
    
    public static function getNavigationBadge(): ?string
    {
        return 'Pronto';
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}

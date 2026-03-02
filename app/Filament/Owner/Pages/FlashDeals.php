<?php

namespace App\Filament\Owner\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class FlashDeals extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bolt';
    protected static ?string $navigationLabel = 'Ofertas Relampago';
    protected static ?string $title = 'Ofertas Relampago';
    protected static ?string $navigationGroup = 'Mi Negocio';
    protected static ?int $navigationSort = 21;

    protected static string $view = 'filament.owner.pages.coming-soon';

    public string $featureName = 'Ofertas Relampago';
    public string $featureDescription = 'Muy pronto podras crear ofertas por tiempo limitado para atraer clientes. Happy Hour, descuentos flash y promociones especiales.';

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        $restaurant = $user->restaurants()->first();
        return $restaurant && $restaurant->is_claimed;
    }

    public static function getNavigationBadge(): ?string
    {
        return 'Pronto';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'gray';
    }
}

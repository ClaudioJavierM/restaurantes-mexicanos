<?php

namespace App\Filament\Owner\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class PickupOrders extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Pedidos Pickup';
    protected static ?string $title = 'Pedidos Pickup';
    protected static ?string $navigationGroup = 'Mi Negocio';
    protected static ?int $navigationSort = 9;
    
    protected static string $view = 'filament.owner.pages.coming-soon';

    public string $featureName = 'Pedidos Pickup';
    public string $featureDescription = 'Muy pronto tus clientes podran hacer pedidos para recoger directamente desde tu perfil. Ordenes en linea, notificaciones y seguimiento en tiempo real.';

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

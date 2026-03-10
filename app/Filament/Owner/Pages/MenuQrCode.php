<?php

namespace App\Filament\Owner\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MenuQrCode extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'Codigo QR';
    protected static ?string $title = 'Codigo QR del Menu';
    protected static ?string $navigationGroup = 'Contenido';
    protected static ?int $navigationSort = 2;
    
    protected static string $view = 'filament.owner.pages.menu-qr-code';

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

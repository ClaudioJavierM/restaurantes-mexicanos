<?php

namespace App\Filament\Owner\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class WidgetEmbed extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';
    protected static ?string $navigationLabel = 'Widget Web';
    protected static ?string $title = 'Widget para tu Sitio Web';
    protected static ?string $navigationGroup = 'Contenido';
    protected static ?int $navigationSort = 6;
    
    protected static string $view = 'filament.owner.pages.coming-soon';

    public string $featureName = 'Widget para tu Sitio Web';
    public string $featureDescription = 'Muy pronto podras insertar un widget en tu sitio web para mostrar tus resenas, menu y boton de reservaciones directamente a tus visitantes.';

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

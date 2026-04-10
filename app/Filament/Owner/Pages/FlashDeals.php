<?php

namespace App\Filament\Owner\Pages;

use App\Models\Restaurant;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class FlashDeals extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bolt';
    protected static ?string $navigationLabel = 'Ofertas Relampago';
    protected static ?string $title = 'Ofertas Relampago';
    protected static ?string $navigationGroup = 'Mi Negocio';
    protected static ?int $navigationSort = 21;

    protected static string $view = 'filament.owner.pages.flash-deals';

    public ?Restaurant $restaurant = null;

    public function mount(): void
    {
        $user = Auth::user();
        if ($user) {
            $this->restaurant = $user->allAccessibleRestaurants()->first();
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        $restaurant = $user->allAccessibleRestaurants()->first();
        return $restaurant && $restaurant->is_claimed;
    }

    public static function getNavigationBadge(): ?string
    {
        return null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}

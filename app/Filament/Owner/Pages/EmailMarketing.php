<?php

namespace App\Filament\Owner\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class EmailMarketing extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = 'Email Marketing';
    protected static ?string $title = 'Email Marketing';
    protected static ?string $navigationGroup = 'Mi Negocio';
    protected static ?int $navigationSort = 6;

    protected static string $view = 'filament.owner.pages.email-marketing';

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

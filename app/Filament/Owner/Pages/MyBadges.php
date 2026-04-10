<?php

namespace App\Filament\Owner\Pages;

use Filament\Pages\Page;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

class MyBadges extends Page
{
    protected static bool $isLazy = true;
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationLabel = 'Mis Badges';
    protected static ?string $title = 'Badges & Reconocimientos FAMER';
    protected static ?string $navigationGroup = 'Reseñas & Reputación';
    protected static ?int $navigationSort = 34;

    protected static string $view = 'filament.owner.pages.my-badges';

    public ?int $restaurantId = null;

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check();
    }

    public function mount(): void
    {
        $restaurant = Restaurant::where('user_id', Auth::id())->first();
        $this->restaurantId = $restaurant?->id;
    }
}

<?php

namespace App\Filament\Owner\Pages;

use Filament\Pages\Page;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

class CompetitorAnalysis extends Page
{
    protected static bool $isLazy = true;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Competidores';
    protected static ?string $title = 'Análisis de Competencia';
    protected static ?string $navigationGroup = 'Reseñas & Reputación';
    protected static ?int $navigationSort = 33;

    protected static string $view = 'filament.owner.pages.competitor-analysis';

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

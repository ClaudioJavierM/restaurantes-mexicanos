<?php

namespace App\Filament\Owner\Pages;

use Filament\Pages\Page;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

class SentimentAnalysis extends Page
{
    protected static bool $isLazy = true;
    protected static ?string $navigationIcon = 'heroicon-o-face-smile';
    protected static ?string $navigationLabel = 'Sentimiento IA';
    protected static ?string $title = 'Análisis de Sentimiento';
    protected static ?string $navigationGroup = 'Reseñas & Reputación';
    protected static ?int $navigationSort = 32;

    protected static string $view = 'filament.owner.pages.sentiment-analysis';

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

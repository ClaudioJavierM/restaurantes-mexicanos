<?php

namespace App\Filament\Owner\Pages;

use Filament\Pages\Page;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

class ReplyReviews extends Page
{
    protected static bool $isLazy = true;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Responder Reseñas';
    protected static ?string $title = 'Responder Reseñas de Clientes';
    protected static ?string $navigationGroup = 'Reseñas & Reputación';
    protected static ?int $navigationSort = 31;

    protected static string $view = 'filament.owner.pages.reply-reviews';

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

<?php

namespace App\Filament\Owner\Pages;

use Filament\Pages\Page;
use App\Models\WidgetToken;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class WidgetEmbed extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';
    protected static ?string $navigationLabel = 'Widget Web';
    protected static ?string $title = 'Widget para tu Sitio Web';
    protected static ?string $navigationGroup = 'Contenido';
    protected static ?int $navigationSort = 6;

    protected static string $view = 'filament.owner.pages.widget-embed';

    public $restaurant;
    public $widgetToken;
    public $isPremium = false;
    public $embedCode = '';
    public $previewUrl = '';

    public function mount(): void
    {
        $this->restaurant = Auth::user()->restaurants()->first();
        
        if ($this->restaurant) {
            $this->isPremium = in_array($this->restaurant->subscription_plan, ['premium', 'elite']);
            $this->widgetToken = $this->restaurant->widgetTokens()->where('is_active', true)->first();
            
            if (!$this->widgetToken && $this->isPremium) {
                $this->widgetToken = WidgetToken::createForRestaurant($this->restaurant->id);
            }
            
            if ($this->widgetToken) {
                $this->generateEmbedCode();
            }
        }
    }

    public function generateEmbedCode(): void
    {
        $baseUrl = config('app.url');
        $this->embedCode = '<div id="famer-widget-' . $this->widgetToken->token . '"></div>
<script src="' . $baseUrl . '/widget.js" data-token="' . $this->widgetToken->token . '" async></script>';
        $this->previewUrl = $baseUrl . '/widget/preview/' . $this->widgetToken->token;
    }

    public function regenerateToken(): void
    {
        if (!$this->isPremium) return;
        
        if ($this->widgetToken) {
            $this->widgetToken->delete();
        }
        
        $this->widgetToken = WidgetToken::createForRestaurant($this->restaurant->id);
        $this->generateEmbedCode();
        
        Notification::make()
            ->title('Token regenerado')
            ->success()
            ->send();
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        $restaurant = $user->restaurants()->first();
        return $restaurant && $restaurant->is_claimed;
    }

    public static function getNavigationBadge(): ?string
    {
        $restaurant = Auth::user()?->restaurants()->first();
        if ($restaurant && !in_array($restaurant->subscription_plan, ['premium', 'elite'])) {
            return 'PRO';
        }
        return null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}

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

    public $restaurant;
    public $menuUrl;
    public $qrSize = 300;
    public $selectedStyle = 'classic';
    public $isPremium = false;

    public function mount(): void
    {
        $this->restaurant = Auth::user()->restaurants()->first();
        
        if ($this->restaurant) {
            $this->menuUrl = url('/restaurante/' . $this->restaurant->slug . '#menu');
            $this->isPremium = in_array($this->restaurant->subscription_plan, ['premium', 'elite']);
        }
    }

    public function getQrCodeUrl(int $size = 300, string $style = 'classic'): string
    {
        if (!$this->menuUrl) {
            return '';
        }

        $encodedUrl = urlencode($this->menuUrl);
        return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$encodedUrl}&format=png&margin=10";
    }

    public function downloadQr(): void
    {
        // Handled by JavaScript
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
        $user = Auth::user();
        if (!$user) return null;
        
        $restaurant = $user->restaurants()->first();
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

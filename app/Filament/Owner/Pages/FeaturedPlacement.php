<?php

namespace App\Filament\Owner\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use App\Models\FeaturedPlacement as FeaturedPlacementModel;

class FeaturedPlacement extends Page
{
    protected static bool $isLazy = true;

    protected static ?string $navigationIcon  = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'Mi Negocio';
    protected static ?int    $navigationSort  = 22;
    protected static ?string $navigationLabel = 'Destacar Restaurante';
    protected static string  $view            = 'filament.owner.pages.featured-placement';

    // ─── Helpers for view ─────────────────────────────────────────────────────

    public function getRestaurant()
    {
        return auth()->user()->restaurants()->first();
    }

    public function getActivePlacementProperty(): ?FeaturedPlacementModel
    {
        $restaurant = $this->getRestaurant();

        if (! $restaurant) {
            return null;
        }

        return FeaturedPlacementModel::active()
            ->where('restaurant_id', $restaurant->id)
            ->first();
    }

    public function getStatsProperty(): array
    {
        $restaurant = $this->getRestaurant();

        if (! $restaurant) {
            return ['impressions' => 0, 'clicks' => 0, 'ctr' => 0];
        }

        $placement = FeaturedPlacementModel::where('restaurant_id', $restaurant->id)
            ->where('status', 'active')
            ->first();

        if (! $placement) {
            return ['impressions' => 0, 'clicks' => 0, 'ctr' => 0];
        }

        $ctr = $placement->impressions > 0
            ? round(($placement->clicks / $placement->impressions) * 100, 1)
            : 0;

        return [
            'impressions' => $placement->impressions,
            'clicks'      => $placement->clicks,
            'ctr'         => $ctr,
        ];
    }

    // ─── Actions ──────────────────────────────────────────────────────────────

    public function requestPlan(string $plan): void
    {
        Notification::make()
            ->title('¡Solicitud recibida!')
            ->body('Te contactaremos en 24 horas para procesar tu pago.')
            ->success()
            ->duration(6000)
            ->send();
    }
}

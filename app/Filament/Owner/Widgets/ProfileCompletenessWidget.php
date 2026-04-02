<?php

namespace App\Filament\Owner\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class ProfileCompletenessWidget extends Widget
{
    protected static string $view = 'filament.owner.widgets.profile-completeness-widget';

    protected static bool $isLazy = true;

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public function getRestaurant()
    {
        return Auth::user()->restaurants()->first();
    }

    public function getCompletenessData(): array
    {
        $restaurant = $this->getRestaurant();

        if (!$restaurant) {
            return ['score' => 0, 'items' => [], 'restaurant' => null];
        }

        // Check photos — uses Spatie Media Library collection 'images' or JSON column
        $hasPhotos = $restaurant->getMedia('images')->count() > 0
            || (!empty($restaurant->photos) && is_array($restaurant->photos) && count($restaurant->photos) > 0);

        // Check menu items via HasManyThrough relationship
        $hasMenuItems = $restaurant->menuItems()->exists();

        $items = [
            [
                'label'  => 'Fotos del restaurante',
                'done'   => $hasPhotos,
                'points' => 20,
                'action' => 'Agregar fotos',
                'url'    => '/owner/' . $restaurant->id . '?tab=photos',
            ],
            [
                'label'  => 'Horarios de atención',
                'done'   => !empty($restaurant->hours),
                'points' => 15,
                'action' => 'Agregar horarios',
                'url'    => '/owner/' . $restaurant->id . '?tab=hours',
            ],
            [
                'label'  => 'Descripción del negocio',
                'done'   => !empty($restaurant->description) || !empty($restaurant->ai_description),
                'points' => 15,
                'action' => 'Agregar descripción',
                'url'    => '/owner/' . $restaurant->id . '?tab=info',
            ],
            [
                'label'  => 'Menú digital',
                'done'   => $hasMenuItems,
                'points' => 20,
                'action' => 'Agregar menú',
                'url'    => '/owner/' . $restaurant->id . '?tab=menu',
            ],
            [
                'label'  => 'Número de teléfono',
                'done'   => !empty($restaurant->phone),
                'points' => 10,
                'action' => 'Agregar teléfono',
                'url'    => '/owner/' . $restaurant->id . '?tab=info',
            ],
            [
                'label'  => 'Dirección completa',
                'done'   => !empty($restaurant->address),
                'points' => 10,
                'action' => 'Agregar dirección',
                'url'    => '/owner/' . $restaurant->id . '?tab=info',
            ],
            [
                'label'  => 'Plan Premium activo',
                'done'   => in_array($restaurant->subscription_tier, ['premium', 'elite']),
                'points' => 10,
                'action' => 'Ver planes',
                'url'    => '/for-owners#pricing',
            ],
        ];

        $score = collect($items)->where('done', true)->sum('points');

        return [
            'score'      => $score,
            'items'      => $items,
            'restaurant' => $restaurant,
        ];
    }
}

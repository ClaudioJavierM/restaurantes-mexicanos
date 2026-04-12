<?php

namespace App\Filament\Owner\Widgets;

use Filament\Widgets\Widget;

class RestaurantSwitcherWidget extends Widget
{
    protected static string $view = 'filament.owner.widgets.restaurant-switcher-widget';

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->allAccessibleRestaurants()->count() > 1;
    }
}

<?php

namespace App\Filament\Owner\Resources\MyCustomersResource\Pages;

use App\Filament\Owner\Resources\MyCustomersResource;
use App\Models\RestaurantCustomer;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListMyCustomers extends ListRecords
{
    protected static string $resource = MyCustomersResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    /**
     * Stats bar rendered inline above the table via subheading.
     */
    public function getSubheading(): ?string
    {
        $restaurantIds = auth()->user()->restaurants()->pluck('id');

        if ($restaurantIds->isEmpty()) {
            return null;
        }

        $total         = RestaurantCustomer::whereIn('restaurant_id', $restaurantIds)->count();
        $subscribed    = RestaurantCustomer::whereIn('restaurant_id', $restaurantIds)->subscribed()->count();
        $avgVisits     = RestaurantCustomer::whereIn('restaurant_id', $restaurantIds)->avg('visits_count') ?? 0;
        $birthdays     = RestaurantCustomer::whereIn('restaurant_id', $restaurantIds)->birthdayThisMonth()->count();

        return sprintf(
            '👥 %d clientes  ·  ✉️ %d suscritos  ·  📊 %.1f visitas promedio  ·  🎂 %d cumpleaños este mes',
            $total,
            $subscribed,
            $avgVisits,
            $birthdays
        );
    }

    public function getTabs(): array
    {
        $restaurantIds = auth()->user()->restaurants()->pluck('id');

        return [
            'all' => Tab::make('Todos')
                ->icon('heroicon-o-users'),

            'subscribed' => Tab::make('Suscritos')
                ->icon('heroicon-o-envelope')
                ->badge(
                    RestaurantCustomer::whereIn('restaurant_id', $restaurantIds)
                        ->subscribed()
                        ->count()
                )
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->subscribed()),

            'inactive' => Tab::make('Inactivos')
                ->icon('heroicon-o-clock')
                ->badge(
                    RestaurantCustomer::whereIn('restaurant_id', $restaurantIds)
                        ->inactive(90)
                        ->count()
                )
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->inactive(90)),

            'birthdays' => Tab::make('Cumpleaños')
                ->icon('heroicon-o-cake')
                ->badge(
                    RestaurantCustomer::whereIn('restaurant_id', $restaurantIds)
                        ->birthdayThisMonth()
                        ->count()
                )
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->birthdayThisMonth()),

            'loyal' => Tab::make('Frecuentes')
                ->icon('heroicon-o-star')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('visits_count', '>', 5)),
        ];
    }
}

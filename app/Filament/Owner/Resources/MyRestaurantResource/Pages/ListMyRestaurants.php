<?php

namespace App\Filament\Owner\Resources\MyRestaurantResource\Pages;

use App\Filament\Owner\Resources\MyRestaurantResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyRestaurants extends ListRecords
{
    protected static string $resource = MyRestaurantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action - owners get restaurants through claim
        ];
    }
}

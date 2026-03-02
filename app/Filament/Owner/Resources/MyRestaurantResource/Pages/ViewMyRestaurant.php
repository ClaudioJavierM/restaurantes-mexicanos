<?php

namespace App\Filament\Owner\Resources\MyRestaurantResource\Pages;

use App\Filament\Owner\Resources\MyRestaurantResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMyRestaurant extends ViewRecord
{
    protected static string $resource = MyRestaurantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

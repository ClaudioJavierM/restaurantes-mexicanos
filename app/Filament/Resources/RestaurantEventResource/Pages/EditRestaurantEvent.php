<?php

namespace App\Filament\Resources\RestaurantEventResource\Pages;

use App\Filament\Resources\RestaurantEventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRestaurantEvent extends EditRecord
{
    protected static string $resource = RestaurantEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

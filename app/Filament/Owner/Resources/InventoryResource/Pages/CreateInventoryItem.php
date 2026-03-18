<?php

namespace App\Filament\Owner\Resources\InventoryResource\Pages;

use App\Filament\Owner\Resources\InventoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInventoryItem extends CreateRecord
{
    protected static string $resource = InventoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $restaurant = auth()->user()->allAccessibleRestaurants()->first();
        $data['restaurant_id'] = $restaurant->id;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

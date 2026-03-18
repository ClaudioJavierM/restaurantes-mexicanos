<?php

namespace App\Filament\Owner\Resources\MenuAbTestsResource\Pages;

use App\Filament\Owner\Resources\MenuAbTestsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMenuAbTest extends CreateRecord
{
    protected static string $resource = MenuAbTestsResource::class;

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

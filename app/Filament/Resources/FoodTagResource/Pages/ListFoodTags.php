<?php

namespace App\Filament\Resources\FoodTagResource\Pages;

use App\Filament\Resources\FoodTagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFoodTags extends ListRecords
{
    protected static string $resource = FoodTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\FoodTagResource\Pages;

use App\Filament\Resources\FoodTagResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFoodTag extends EditRecord
{
    protected static string $resource = FoodTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

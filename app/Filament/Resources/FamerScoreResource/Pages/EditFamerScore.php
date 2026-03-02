<?php

namespace App\Filament\Resources\FamerScoreResource\Pages;

use App\Filament\Resources\FamerScoreResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFamerScore extends EditRecord
{
    protected static string $resource = FamerScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

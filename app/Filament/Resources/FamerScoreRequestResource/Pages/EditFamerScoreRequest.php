<?php

namespace App\Filament\Resources\FamerScoreRequestResource\Pages;

use App\Filament\Resources\FamerScoreRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFamerScoreRequest extends EditRecord
{
    protected static string $resource = FamerScoreRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

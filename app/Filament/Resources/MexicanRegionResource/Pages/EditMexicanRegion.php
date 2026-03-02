<?php

namespace App\Filament\Resources\MexicanRegionResource\Pages;

use App\Filament\Resources\MexicanRegionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMexicanRegion extends EditRecord
{
    protected static string $resource = MexicanRegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

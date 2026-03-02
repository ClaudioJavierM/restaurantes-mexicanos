<?php

namespace App\Filament\Resources\MexicanRegionResource\Pages;

use App\Filament\Resources\MexicanRegionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMexicanRegions extends ListRecords
{
    protected static string $resource = MexicanRegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

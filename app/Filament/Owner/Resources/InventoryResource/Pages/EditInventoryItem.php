<?php

namespace App\Filament\Owner\Resources\InventoryResource\Pages;

use App\Filament\Owner\Resources\InventoryResource;
use Filament\Resources\Pages\EditRecord;

class EditInventoryItem extends EditRecord
{
    protected static string $resource = InventoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

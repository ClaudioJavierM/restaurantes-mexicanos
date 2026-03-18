<?php

namespace App\Filament\Owner\Resources\InventoryResource\Pages;

use App\Filament\Owner\Resources\InventoryResource;
use Filament\Resources\Pages\ListRecords;

class ListInventoryItems extends ListRecords
{
    protected static string $resource = InventoryResource::class;
}

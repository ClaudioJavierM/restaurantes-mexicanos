<?php

namespace App\Filament\Owner\Resources\MyReservationsResource\Pages;

use App\Filament\Owner\Resources\MyReservationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyReservations extends ListRecords
{
    protected static string $resource = MyReservationsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

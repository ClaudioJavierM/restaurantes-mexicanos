<?php

namespace App\Filament\Owner\Resources\WaitlistResource\Pages;

use App\Filament\Owner\Resources\WaitlistResource;
use Filament\Resources\Pages\ListRecords;

class ListWaitlist extends ListRecords
{
    protected static string $resource = WaitlistResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

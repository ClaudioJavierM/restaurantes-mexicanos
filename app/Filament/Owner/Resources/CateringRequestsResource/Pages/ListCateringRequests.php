<?php

namespace App\Filament\Owner\Resources\CateringRequestsResource\Pages;

use App\Filament\Owner\Resources\CateringRequestsResource;
use Filament\Resources\Pages\ListRecords;

class ListCateringRequests extends ListRecords
{
    protected static string $resource = CateringRequestsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

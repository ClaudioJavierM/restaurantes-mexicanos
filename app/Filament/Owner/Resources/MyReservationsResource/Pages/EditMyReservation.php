<?php

namespace App\Filament\Owner\Resources\MyReservationsResource\Pages;

use App\Filament\Owner\Resources\MyReservationsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyReservation extends EditRecord
{
    protected static string $resource = MyReservationsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

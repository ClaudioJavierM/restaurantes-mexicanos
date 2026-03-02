<?php

namespace App\Filament\Resources\RestaurantReportResource\Pages;

use App\Filament\Resources\RestaurantReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRestaurantReport extends EditRecord
{
    protected static string $resource = RestaurantReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

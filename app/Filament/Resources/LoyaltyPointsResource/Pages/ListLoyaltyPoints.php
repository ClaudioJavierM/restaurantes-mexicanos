<?php

namespace App\Filament\Resources\LoyaltyPointsResource\Pages;

use App\Filament\Resources\LoyaltyPointsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoyaltyPoints extends ListRecords
{
    protected static string $resource = LoyaltyPointsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

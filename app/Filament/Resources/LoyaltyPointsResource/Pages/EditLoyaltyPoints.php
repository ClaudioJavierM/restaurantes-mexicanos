<?php

namespace App\Filament\Resources\LoyaltyPointsResource\Pages;

use App\Filament\Resources\LoyaltyPointsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoyaltyPoints extends EditRecord
{
    protected static string $resource = LoyaltyPointsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\FamerSubscriptionResource\Pages;

use App\Filament\Resources\FamerSubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFamerSubscription extends EditRecord
{
    protected static string $resource = FamerSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

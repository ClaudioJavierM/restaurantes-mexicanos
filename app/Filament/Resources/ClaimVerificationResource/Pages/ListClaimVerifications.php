<?php

namespace App\Filament\Resources\ClaimVerificationResource\Pages;

use App\Filament\Resources\ClaimVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClaimVerifications extends ListRecords
{
    protected static string $resource = ClaimVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

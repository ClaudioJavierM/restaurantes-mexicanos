<?php

namespace App\Filament\Resources\ClaimVerificationResource\Pages;

use App\Filament\Resources\ClaimVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClaimVerification extends EditRecord
{
    protected static string $resource = ClaimVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

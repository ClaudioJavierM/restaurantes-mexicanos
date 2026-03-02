<?php

namespace App\Filament\Owner\Resources\MyCouponsResource\Pages;

use App\Filament\Owner\Resources\MyCouponsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyCoupon extends EditRecord
{
    protected static string $resource = MyCouponsResource::class;

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

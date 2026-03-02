<?php

namespace App\Filament\Owner\Resources\MyPhotosResource\Pages;

use App\Filament\Owner\Resources\MyPhotosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyPhoto extends EditRecord
{
    protected static string $resource = MyPhotosResource::class;

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

<?php

namespace App\Filament\Owner\Resources\MyMenuResource\Pages;

use App\Filament\Owner\Resources\MyMenuResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateMyMenuItem extends CreateRecord
{
    protected static string $resource = MyMenuResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Platillo creado')
            ->body('El platillo ha sido agregado a tu menú exitosamente.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

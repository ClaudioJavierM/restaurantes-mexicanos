<?php

namespace App\Filament\Owner\Resources\MyMenuResource\Pages;

use App\Filament\Owner\Resources\MyMenuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditMyMenuItem extends EditRecord
{
    protected static string $resource = MyMenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Platillo actualizado')
            ->body('Los cambios han sido guardados exitosamente.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

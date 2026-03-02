<?php

namespace App\Filament\Owner\Resources\MyRestaurantResource\Pages;

use App\Filament\Owner\Resources\MyRestaurantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditMyRestaurant extends EditRecord
{
    protected static string $resource = MyRestaurantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Restaurante actualizado')
            ->body('La información de tu restaurante ha sido actualizada exitosamente.');
    }
}

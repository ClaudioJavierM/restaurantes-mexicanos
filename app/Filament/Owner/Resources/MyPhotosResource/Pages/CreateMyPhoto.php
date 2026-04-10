<?php

namespace App\Filament\Owner\Resources\MyPhotosResource\Pages;

use App\Filament\Owner\Resources\MyPhotosResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateMyPhoto extends CreateRecord
{
    protected static string $resource = MyPhotosResource::class;

    public function mount(): void
    {
        if (!MyPhotosResource::canCreate()) {
            $maxPhotos = MyPhotosResource::getMaxPhotos();

            Notification::make()
                ->title('Límite de fotos alcanzado')
                ->body("Has alcanzado el máximo de {$maxPhotos} fotos en tu plan gratuito. Mejora tu plan a Premium o Elite para subir fotos ilimitadas.")
                ->danger()
                ->persistent()
                ->send();

            $this->redirect(MyPhotosResource::getUrl('index'));
            return;
        }

        parent::mount();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function beforeCreate(): void
    {
        if (!MyPhotosResource::canCreate()) {
            Notification::make()
                ->title('Límite de fotos alcanzado')
                ->body('Mejora tu plan para subir más fotos.')
                ->danger()
                ->send();

            $this->halt();
        }
    }

    protected function afterCreate(): void
    {
        $currentCount = MyPhotosResource::getCurrentPhotoCount();
        $maxPhotos = MyPhotosResource::getMaxPhotos();
        $restaurant = auth()->user()->firstAccessibleRestaurant();
        $plan = $restaurant?->subscription_tier ?? 'free';
        $isFree = !in_array($plan, ['premium', 'elite']);

        $body = $isFree
            ? "Has usado {$currentCount} de {$maxPhotos} fotos disponibles en tu plan."
            : "Total de fotos: {$currentCount}";

        Notification::make()
            ->title('Foto subida exitosamente')
            ->body($body)
            ->success()
            ->send();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['approved_at'] = now();
        $data['approved_by'] = auth()->id();

        return $data;
    }
}
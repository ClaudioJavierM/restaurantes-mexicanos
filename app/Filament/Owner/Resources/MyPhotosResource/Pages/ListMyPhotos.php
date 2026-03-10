<?php

namespace App\Filament\Owner\Resources\MyPhotosResource\Pages;

use App\Filament\Owner\Resources\MyPhotosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListMyPhotos extends ListRecords
{
    protected static string $resource = MyPhotosResource::class;

    protected function getHeaderActions(): array
    {
        $canCreate = MyPhotosResource::canCreate();
        $restaurant = auth()->user()->allAccessibleRestaurants()->first();
        $plan = $restaurant?->subscription_tier ?? 'free';
        $isFree = !in_array($plan, ['premium', 'elite']);
        $currentCount = MyPhotosResource::getCurrentPhotoCount();
        $maxPhotos = MyPhotosResource::getMaxPhotos();

        $actions = [];

        if (!$canCreate && $isFree) {
            $actions[] = Actions\Action::make('upgrade')
                ->label('Mejorar Plan para más fotos')
                ->icon('heroicon-o-arrow-up-circle')
                ->color('warning')
                ->url(route('filament.owner.pages.upgrade-subscription'))
                ->openUrlInNewTab(false);
        }

        $actions[] = Actions\CreateAction::make()
            ->label('Subir Foto')
            ->disabled(!$canCreate)
            ->tooltip(!$canCreate ? "Has alcanzado el límite de {$maxPhotos} fotos de tu plan gratuito. Mejora tu plan para subir más." : null);

        return $actions;
    }

    public function getHeading(): string
    {
        $currentCount = MyPhotosResource::getCurrentPhotoCount();
        $maxPhotos = MyPhotosResource::getMaxPhotos();
        $restaurant = auth()->user()->allAccessibleRestaurants()->first();
        $plan = $restaurant?->subscription_tier ?? 'free';
        $isFree = !in_array($plan, ['premium', 'elite']);

        if ($isFree) {
            return "Galería de Fotos ({$currentCount}/{$maxPhotos})";
        }

        return "Galería de Fotos ({$currentCount})";
    }

    public function getSubheading(): ?string
    {
        $canCreate = MyPhotosResource::canCreate();
        $restaurant = auth()->user()->allAccessibleRestaurants()->first();
        $plan = $restaurant?->subscription_tier ?? 'free';
        $isFree = !in_array($plan, ['premium', 'elite']);

        if (!$canCreate && $isFree) {
            return '⚠️ Has alcanzado el límite de fotos de tu plan gratuito. ¡Mejora tu plan para subir fotos ilimitadas!';
        }

        return null;
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
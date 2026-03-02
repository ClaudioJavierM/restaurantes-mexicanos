<?php

namespace App\Filament\Owner\Resources\MyMenuResource\Pages;

use App\Filament\Owner\Resources\MyMenuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListMyMenu extends ListRecords
{
    protected static string $resource = MyMenuResource::class;

    protected function getHeaderActions(): array
    {
        $restaurant = auth()->user()->restaurants()->first();
        $plan = $restaurant?->subscription_tier ?? 'free';
        $hasPremium = in_array($plan, ['premium', 'elite']);

        $actions = [];

        if ($hasPremium) {
            $actions[] = Actions\Action::make('upload_menu')
                ->label('Subir Menú con IA')
                ->icon('heroicon-o-sparkles')
                ->url(fn () => MyMenuResource::getUrl('upload'))
                ->color('success');
        } else {
            $actions[] = Actions\Action::make('upload_menu')
                ->label('Subir Menú con IA')
                ->icon('heroicon-o-sparkles')
                ->color('gray')
                ->action(function () {
                    Notification::make()
                        ->title('Función Premium')
                        ->body('La carga de menú con IA está disponible en los planes Premium y Elite. ¡Mejora tu plan para acceder a esta función!')
                        ->warning()
                        ->persistent()
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('upgrade')
                                ->label('Mejorar Plan')
                                ->url(route('filament.owner.pages.upgrade-subscription'))
                                ->button()
                                ->color('warning'),
                        ])
                        ->send();
                });
        }

        $actions[] = Actions\CreateAction::make()
            ->label('Agregar Platillo');

        return $actions;
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
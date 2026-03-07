<?php

namespace App\Filament\Owner\Resources\MyMenuResource\Pages;

use App\Filament\Owner\Resources\MyMenuResource;
use App\Models\Restaurant;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;

class UploadMenu extends Page
{
    protected static string $resource = MyMenuResource::class;

    protected static string $view = 'filament.owner.pages.upload-menu';

    protected static ?string $title = 'Subir Menú con IA';

    protected static ?string $navigationLabel = 'Subir Menú';

    public ?Restaurant $restaurant = null;

    public function mount(): void
    {
        $this->restaurant = auth()->user()->allAccessibleRestaurants()->first();
        $plan = $this->restaurant?->subscription_tier ?? 'free';

        if (!in_array($plan, ['premium', 'elite'])) {
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

            $this->redirect(MyMenuResource::getUrl('index'));
        }
    }

    public function getViewData(): array
    {
        return [
            'restaurant' => $this->restaurant,
        ];
    }
}

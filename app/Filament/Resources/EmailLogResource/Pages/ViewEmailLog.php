<?php

namespace App\Filament\Resources\EmailLogResource\Pages;

use App\Filament\Resources\EmailLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEmailLog extends ViewRecord
{
    protected static string $resource = EmailLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('resend')
                ->label('Reenviar')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->visible(fn () => in_array($this->record->status, ['failed', 'bounced']))
                ->requiresConfirmation()
                ->modalHeading('¿Reenviar este email?')
                ->modalDescription('Se intentará enviar el email nuevamente.')
                ->action(function () {
                    $this->record->update([
                        'status' => 'pending',
                        'error_message' => null,
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Email marcado para reenvío')
                        ->success()
                        ->send();
                }),
        ];
    }
}

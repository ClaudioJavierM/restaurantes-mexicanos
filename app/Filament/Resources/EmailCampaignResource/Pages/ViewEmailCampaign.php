<?php

namespace App\Filament\Resources\EmailCampaignResource\Pages;

use App\Filament\Resources\EmailCampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;

class ViewEmailCampaign extends ViewRecord
{
    protected static string $resource = EmailCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => in_array($this->record->status, ['draft', 'paused', 'scheduled'])),

            Actions\Action::make('send')
                ->label('Enviar Ahora')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('¿Enviar campaña ahora?')
                ->modalDescription(fn () => "Esta acción enviará la campaña \"{$this->record->name}\" a todos los destinatarios.")
                ->action(function () {
                    dispatch(new \App\Jobs\SendEmailCampaign($this->record));
                    Notification::make()
                        ->title('Campaña iniciada')
                        ->body('La campaña se está enviando en segundo plano.')
                        ->success()
                        ->send();
                })
                ->visible(fn () => in_array($this->record->status, ['draft', 'scheduled'])),

            Actions\Action::make('refresh_stats')
                ->label('Actualizar Estadísticas')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    $this->record->syncStats();
                    $this->refreshFormData(['total_recipients', 'sent_count', 'opened_count', 'clicked_count', 'bounced_count', 'failed_count']);
                    Notification::make()
                        ->title('Estadísticas actualizadas')
                        ->success()
                        ->send();
                })
                ->visible(fn () => $this->record->status !== 'draft'),
        ];
    }
}

<?php

namespace App\Filament\Resources\EmailCampaignResource\Pages;

use App\Filament\Resources\EmailCampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditEmailCampaign extends EditRecord
{
    protected static string $resource = EmailCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),

            Actions\Action::make('preview')
                ->label('Vista Previa')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn () => route('email.preview', $this->record->id))
                ->openUrlInNewTab(),

            Actions\Action::make('test_send')
                ->label('Enviar Prueba')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\TextInput::make('test_email')
                        ->label('Email de Prueba')
                        ->email()
                        ->required()
                        ->default(auth()->user()->email),
                ])
                ->action(function (array $data) {
                    // Send test email
                    dispatch(new \App\Jobs\SendTestCampaignEmail($this->record, $data['test_email']));

                    Notification::make()
                        ->title('Email de prueba enviado')
                        ->body("Se ha enviado un email de prueba a {$data['test_email']}")
                        ->success()
                        ->send();
                }),

            Actions\DeleteAction::make()
                ->visible(fn () => in_array($this->record->status, ['draft', 'cancelled'])),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

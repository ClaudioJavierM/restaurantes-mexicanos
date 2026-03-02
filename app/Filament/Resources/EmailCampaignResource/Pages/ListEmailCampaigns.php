<?php

namespace App\Filament\Resources\EmailCampaignResource\Pages;

use App\Filament\Resources\EmailCampaignResource;
use App\Filament\Resources\EmailCampaignResource\Widgets\CampaignStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListEmailCampaigns extends ListRecords
{
    protected static string $resource = EmailCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nueva Campaña'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CampaignStatsWidget::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todas')
                ->badge(fn () => static::getResource()::getModel()::count()),

            'draft' => Tab::make('Borradores')
                ->badge(fn () => static::getResource()::getModel()::where('status', 'draft')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'draft')),

            'scheduled' => Tab::make('Programadas')
                ->badge(fn () => static::getResource()::getModel()::where('status', 'scheduled')->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'scheduled')),

            'sending' => Tab::make('Enviando')
                ->badge(fn () => static::getResource()::getModel()::where('status', 'sending')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'sending')),

            'sent' => Tab::make('Enviadas')
                ->badge(fn () => static::getResource()::getModel()::where('status', 'sent')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'sent')),
        ];
    }
}

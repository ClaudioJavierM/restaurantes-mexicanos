<?php

namespace App\Filament\Resources\EmailLogResource\Pages;

use App\Filament\Resources\EmailLogResource;
use App\Filament\Resources\EmailLogResource\Widgets\EmailStatsOverview;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListEmailLogs extends ListRecords
{
    protected static string $resource = EmailLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EmailStatsOverview::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todos')
                ->badge($this->getModel()::count()),
            'sent' => Tab::make('Enviados')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['sent', 'delivered', 'opened', 'clicked']))
                ->badge($this->getModel()::whereIn('status', ['sent', 'delivered', 'opened', 'clicked'])->count())
                ->badgeColor('success'),
            'opened' => Tab::make('Abiertos')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('opened_at'))
                ->badge($this->getModel()::whereNotNull('opened_at')->count())
                ->badgeColor('info'),
            'clicked' => Tab::make('Con Clicks')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('clicked_at'))
                ->badge($this->getModel()::whereNotNull('clicked_at')->count())
                ->badgeColor('primary'),
            'failed' => Tab::make('Fallidos')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['failed', 'bounced']))
                ->badge($this->getModel()::whereIn('status', ['failed', 'bounced'])->count())
                ->badgeColor('danger'),
            'pending' => Tab::make('Pendientes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge($this->getModel()::where('status', 'pending')->count())
                ->badgeColor('warning'),
        ];
    }
}

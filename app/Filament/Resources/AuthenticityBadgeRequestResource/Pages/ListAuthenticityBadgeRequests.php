<?php

namespace App\Filament\Resources\AuthenticityBadgeRequestResource\Pages;

use App\Filament\Resources\AuthenticityBadgeRequestResource;
use App\Models\AuthenticityBadgeRequest;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAuthenticityBadgeRequests extends ListRecords
{
    protected static string $resource = AuthenticityBadgeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todas')
                ->badge(AuthenticityBadgeRequest::count()),

            'pending' => Tab::make('Pendientes')
                ->badge(AuthenticityBadgeRequest::pending()->count())
                ->modifyQueryUsing(fn (Builder $q) => $q->pending()),

            'approved' => Tab::make('Aprobadas')
                ->badge(AuthenticityBadgeRequest::approved()->count())
                ->modifyQueryUsing(fn (Builder $q) => $q->approved()),

            'rejected' => Tab::make('Rechazadas')
                ->badge(AuthenticityBadgeRequest::where('status', 'rejected')->count())
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'rejected')),
        ];
    }
}

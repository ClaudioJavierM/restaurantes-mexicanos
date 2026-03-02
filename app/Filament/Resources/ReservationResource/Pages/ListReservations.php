<?php

namespace App\Filament\Resources\ReservationResource\Pages;

use App\Filament\Resources\ReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListReservations extends ListRecords
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'upcoming' => Tab::make('Proximas')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('reservation_date', '>=', now()->toDateString())
                    ->whereIn('status', ['pending', 'confirmed']))
                ->badge(fn () => static::getModel()::where('reservation_date', '>=', now()->toDateString())
                    ->whereIn('status', ['pending', 'confirmed'])->count()),
            'today' => Tab::make('Hoy')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('reservation_date', now()))
                ->badge(fn () => static::getModel()::whereDate('reservation_date', now())->count()),
            'pending' => Tab::make('Pendientes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(fn () => static::getModel()::where('status', 'pending')->count())
                ->badgeColor('warning'),
            'confirmed' => Tab::make('Confirmadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'confirmed'))
                ->badge(fn () => static::getModel()::where('status', 'confirmed')->count())
                ->badgeColor('success'),
            'all' => Tab::make('Todas')
                ->badge(fn () => static::getModel()::count()),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'upcoming';
    }
}

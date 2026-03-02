<?php

namespace App\Filament\Resources\UserPhotoResource\Pages;

use App\Filament\Resources\UserPhotoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUserPhotos extends ListRecords
{
    protected static string $resource = UserPhotoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action - photos are uploaded by users
        ];
    }

    public function getTabs(): array
    {
        return [
            'pending' => Tab::make('Pendientes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(fn () => static::getModel()::where('status', 'pending')->count())
                ->badgeColor('warning'),
            'approved' => Tab::make('Aprobadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'approved'))
                ->badge(fn () => static::getModel()::where('status', 'approved')->count())
                ->badgeColor('success'),
            'rejected' => Tab::make('Rechazadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'rejected'))
                ->badge(fn () => static::getModel()::where('status', 'rejected')->count())
                ->badgeColor('danger'),
            'all' => Tab::make('Todas')
                ->badge(fn () => static::getModel()::count()),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'pending';
    }
}

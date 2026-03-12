<?php

namespace App\Filament\Resources\FamerSubscriptionResource\Pages;

use App\Filament\Resources\FamerSubscriptionResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListFamerSubscriptions extends ListRecords
{
    protected static string $resource = FamerSubscriptionResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todos')
                ->icon('heroicon-m-star'),
            'elite' => Tab::make('Elite')
                ->icon('heroicon-m-trophy')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('subscription_tier', 'elite')),
            'premium' => Tab::make('Premium')
                ->icon('heroicon-m-bolt')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('subscription_tier', 'premium')),
        ];
    }
}

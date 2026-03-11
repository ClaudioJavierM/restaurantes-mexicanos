<?php

namespace App\Filament\Resources\ClaimVerificationResource\Pages;

use App\Filament\Resources\ClaimVerificationResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListClaimVerifications extends ListRecords
{
    protected static string $resource = ClaimVerificationResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todos')
                ->icon('heroicon-m-users'),
            'elite' => Tab::make('Elite')
                ->icon('heroicon-m-trophy')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('subscription_tier', 'elite')),
            'premium' => Tab::make('Premium')
                ->icon('heroicon-m-bolt')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('subscription_tier', 'premium')),
            'claimed' => Tab::make('Claimed')
                ->icon('heroicon-m-flag')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('subscription_tier', 'claimed')),
            'free' => Tab::make('Free (con dueño)')
                ->icon('heroicon-m-user')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('subscription_tier', 'free')),
        ];
    }
}

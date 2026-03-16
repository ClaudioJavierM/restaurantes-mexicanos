<?php

namespace App\Filament\Resources\FanScoreResource\Pages;

use App\Filament\Resources\FanScoreResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListFanScores extends ListRecords
{
    protected static string $resource = FanScoreResource::class;

    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todos')
                ->icon('heroicon-o-users'),
            'fan_destacado' => Tab::make('Fan Destacado')
                ->icon('heroicon-o-trophy')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('fan_level', 'fan_destacado'))
                ->badge(fn() => \App\Models\FanScore::where('fan_level', 'fan_destacado')->where('year', now()->year)->count()),
            'super_fan' => Tab::make('Super Fan')
                ->icon('heroicon-o-star')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('fan_level', 'super_fan'))
                ->badge(fn() => \App\Models\FanScore::where('fan_level', 'super_fan')->where('year', now()->year)->count()),
            'fan' => Tab::make('Fan')
                ->icon('heroicon-o-heart')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('fan_level', 'fan'))
                ->badge(fn() => \App\Models\FanScore::where('fan_level', 'fan')->where('year', now()->year)->count()),
        ];
    }
}

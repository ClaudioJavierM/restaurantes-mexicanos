<?php

namespace App\Filament\Resources\FamerScoreResource\Pages;

use App\Filament\Resources\FamerScoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListFamerScores extends ListRecords
{
    protected static string $resource = FamerScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('recalculate_expired')
                ->label('Recalculate Expired')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    $this->dispatch('recalculate-expired-scores');
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Scores')
                ->badge($this->getModel()::count()),
            'grade_a' => Tab::make('A Grades')
                ->badge($this->getModel()::where('overall_score', '>=', 90)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('overall_score', '>=', 90)),
            'grade_b' => Tab::make('B Grades')
                ->badge($this->getModel()::whereBetween('overall_score', [80, 89])->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('overall_score', [80, 89])),
            'grade_c' => Tab::make('C Grades')
                ->badge($this->getModel()::whereBetween('overall_score', [70, 79])->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('overall_score', [70, 79])),
            'needs_improvement' => Tab::make('Needs Improvement')
                ->badge($this->getModel()::where('overall_score', '<', 70)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('overall_score', '<', 70)),
            'expired' => Tab::make('Expired')
                ->badge($this->getModel()::where('expires_at', '<', now())->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('expires_at', '<', now())),
        ];
    }
}

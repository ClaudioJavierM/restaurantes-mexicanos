<?php

namespace App\Filament\Resources\FamerScoreRequestResource\Pages;

use App\Filament\Resources\FamerScoreRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListFamerScoreRequests extends ListRecords
{
    protected static string $resource = FamerScoreRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export All Leads')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    $leads = $this->getModel()::all();
                    $csv = "Email,Name,Phone,Restaurant,City,State,Status,Owner,Marketing,Created\n";

                    foreach ($leads as $lead) {
                        $csv .= implode(',', [
                            $lead->email,
                            '"' . ($lead->name ?? '') . '"',
                            $lead->phone ?? '',
                            '"' . ($lead->restaurant_name ?? $lead->restaurant?->name ?? '') . '"',
                            $lead->restaurant_city ?? '',
                            $lead->restaurant_state ?? '',
                            $lead->status,
                            $lead->is_owner ? 'Yes' : 'No',
                            $lead->marketing_consent ? 'Yes' : 'No',
                            $lead->created_at->format('Y-m-d H:i'),
                        ]) . "\n";
                    }

                    return response()->streamDownload(function () use ($csv) {
                        echo $csv;
                    }, 'famer-leads-' . now()->format('Y-m-d') . '.csv');
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Leads')
                ->badge($this->getModel()::count()),
            'pending' => Tab::make('Pending')
                ->badge($this->getModel()::pending()->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->pending()),
            'sent' => Tab::make('Sent')
                ->badge($this->getModel()::where('status', 'sent')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'sent')),
            'opened' => Tab::make('Opened')
                ->badge($this->getModel()::where('status', 'opened')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'opened')),
            'claimed' => Tab::make('Claimed')
                ->badge($this->getModel()::where('status', 'claimed')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'claimed')),
            'owners' => Tab::make('Owners')
                ->badge($this->getModel()::where('is_owner', true)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_owner', true)),
        ];
    }
}

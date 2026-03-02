<?php

namespace App\Filament\Resources\FamerScoreResource\Pages;

use App\Filament\Resources\FamerScoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

class ViewFamerScore extends ViewRecord
{
    protected static string $resource = FamerScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Restaurant Information')
                    ->schema([
                        Components\TextEntry::make('restaurant.name')
                            ->label('Restaurant'),
                        Components\TextEntry::make('restaurant.city')
                            ->label('City'),
                        Components\TextEntry::make('restaurant.state.name')
                            ->label('State'),
                    ])->columns(3),

                Components\Section::make('Score Overview')
                    ->schema([
                        Components\TextEntry::make('overall_score')
                            ->label('Overall Score')
                            ->badge()
                            ->color(fn ($state): string => match (true) {
                                $state >= 90 => 'success',
                                $state >= 70 => 'info',
                                $state >= 50 => 'warning',
                                default => 'danger',
                            })
                            ->size('lg'),
                        Components\TextEntry::make('letter_grade')
                            ->label('Letter Grade')
                            ->badge()
                            ->color(fn ($record): string => $record->grade_color)
                            ->size('lg'),
                        Components\TextEntry::make('percentile')
                            ->label('Percentile')
                            ->suffix('%'),
                        Components\TextEntry::make('score_description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ])->columns(3),

                Components\Section::make('Category Breakdown')
                    ->schema([
                        Components\TextEntry::make('profile_completeness_score')
                            ->label('Profile Completeness (20%)')
                            ->suffix('/100')
                            ->badge()
                            ->color(fn ($state): string => $state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger')),
                        Components\TextEntry::make('online_presence_score')
                            ->label('Online Presence (25%)')
                            ->suffix('/100')
                            ->badge()
                            ->color(fn ($state): string => $state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger')),
                        Components\TextEntry::make('customer_engagement_score')
                            ->label('Customer Engagement (20%)')
                            ->suffix('/100')
                            ->badge()
                            ->color(fn ($state): string => $state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger')),
                        Components\TextEntry::make('menu_offerings_score')
                            ->label('Menu & Offerings (15%)')
                            ->suffix('/100')
                            ->badge()
                            ->color(fn ($state): string => $state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger')),
                        Components\TextEntry::make('mexican_authenticity_score')
                            ->label('Mexican Authenticity (10%)')
                            ->suffix('/100')
                            ->badge()
                            ->color(fn ($state): string => $state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger')),
                        Components\TextEntry::make('digital_readiness_score')
                            ->label('Digital Readiness (10%)')
                            ->suffix('/100')
                            ->badge()
                            ->color(fn ($state): string => $state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger')),
                    ])->columns(3),

                Components\Section::make('Rankings')
                    ->schema([
                        Components\TextEntry::make('area_rank')
                            ->label('Area Rank')
                            ->formatStateUsing(fn ($state, $record): string =>
                                $state ? "#{$state} of {$record->area_total}" : '-'
                            ),
                        Components\TextEntry::make('area_average')
                            ->label('Area Average'),
                        Components\TextEntry::make('category_rank')
                            ->label('Category Rank')
                            ->formatStateUsing(fn ($state, $record): string =>
                                $state ? "#{$state} of {$record->category_total}" : '-'
                            ),
                        Components\TextEntry::make('category_average')
                            ->label('Category Average'),
                    ])->columns(4),

                Components\Section::make('Recommendations')
                    ->schema([
                        Components\RepeatableEntry::make('recommendations')
                            ->schema([
                                Components\TextEntry::make('priority')
                                    ->badge()
                                    ->color(fn ($state): string => match ($state) {
                                        'critical' => 'danger',
                                        'high' => 'warning',
                                        'medium' => 'info',
                                        default => 'gray',
                                    }),
                                Components\TextEntry::make('title'),
                                Components\TextEntry::make('description')
                                    ->columnSpan(2),
                                Components\TextEntry::make('impact')
                                    ->badge()
                                    ->color('success'),
                            ])
                            ->columns(5)
                            ->columnSpanFull(),
                    ]),

                Components\Section::make('Metadata')
                    ->schema([
                        Components\TextEntry::make('calculated_at')
                            ->label('Calculated At')
                            ->dateTime(),
                        Components\TextEntry::make('expires_at')
                            ->label('Expires At')
                            ->dateTime(),
                        Components\TextEntry::make('version')
                            ->label('Version'),
                        Components\TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                    ])->columns(4),
            ]);
    }
}

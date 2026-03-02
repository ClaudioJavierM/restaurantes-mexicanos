<?php

namespace App\Filament\Resources\FamerScoreRequestResource\Pages;

use App\Filament\Resources\FamerScoreRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

class ViewFamerScoreRequest extends ViewRecord
{
    protected static string $resource = FamerScoreRequestResource::class;

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
                Components\Section::make('Contact Information')
                    ->schema([
                        Components\TextEntry::make('email')
                            ->copyable(),
                        Components\TextEntry::make('name'),
                        Components\TextEntry::make('phone'),
                    ])->columns(3),

                Components\Section::make('Restaurant')
                    ->schema([
                        Components\TextEntry::make('restaurant.name')
                            ->label('Linked Restaurant')
                            ->url(fn ($record) => $record->restaurant_id
                                ? route('filament.admin.resources.restaurants.edit', $record->restaurant_id)
                                : null
                            ),
                        Components\TextEntry::make('restaurant_name')
                            ->label('Submitted Name'),
                        Components\TextEntry::make('restaurant_city')
                            ->label('City'),
                        Components\TextEntry::make('restaurant_state')
                            ->label('State'),
                    ])->columns(4),

                Components\Section::make('Status')
                    ->schema([
                        Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'gray',
                                'sent' => 'info',
                                'opened' => 'warning',
                                'clicked' => 'success',
                                'claimed' => 'success',
                                default => 'gray',
                            }),
                        Components\IconEntry::make('is_owner')
                            ->label('Identified as Owner')
                            ->boolean(),
                        Components\IconEntry::make('marketing_consent')
                            ->label('Marketing Consent')
                            ->boolean(),
                    ])->columns(3),

                Components\Section::make('Score Information')
                    ->schema([
                        Components\TextEntry::make('famerScore.overall_score')
                            ->label('Score')
                            ->badge()
                            ->color(fn ($state): string => match (true) {
                                $state >= 90 => 'success',
                                $state >= 70 => 'info',
                                $state >= 50 => 'warning',
                                default => 'danger',
                            }),
                        Components\TextEntry::make('famerScore.letter_grade')
                            ->label('Grade'),
                    ])->columns(2)
                    ->visible(fn ($record) => $record->famer_score_id !== null),

                Components\Section::make('Email Timeline')
                    ->schema([
                        Components\TextEntry::make('email_sent_at')
                            ->label('Sent At')
                            ->dateTime(),
                        Components\TextEntry::make('email_opened_at')
                            ->label('Opened At')
                            ->dateTime(),
                        Components\TextEntry::make('email_clicked_at')
                            ->label('Clicked At')
                            ->dateTime(),
                    ])->columns(3),

                Components\Section::make('UTM Tracking')
                    ->schema([
                        Components\TextEntry::make('utm_source')
                            ->label('Source'),
                        Components\TextEntry::make('utm_medium')
                            ->label('Medium'),
                        Components\TextEntry::make('utm_campaign')
                            ->label('Campaign'),
                        Components\TextEntry::make('referrer')
                            ->label('Referrer'),
                    ])->columns(4)
                    ->collapsed(),

                Components\Section::make('Technical Details')
                    ->schema([
                        Components\TextEntry::make('ip_address'),
                        Components\TextEntry::make('user_agent')
                            ->columnSpanFull(),
                        Components\TextEntry::make('created_at')
                            ->dateTime(),
                        Components\TextEntry::make('updated_at')
                            ->dateTime(),
                    ])->columns(2)
                    ->collapsed(),
            ]);
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FamerScoreResource\Pages;
use App\Models\FamerScore;
use App\Services\FamerScoreService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FamerScoreResource extends Resource
{
    protected static ?string $model = FamerScore::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'FAMER Scores';

    protected static ?string $navigationGroup = 'Analytics';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Restaurant')
                    ->schema([
                        Forms\Components\Select::make('restaurant_id')
                            ->relationship('restaurant', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),

                Forms\Components\Section::make('Score Overview')
                    ->schema([
                        Forms\Components\TextInput::make('overall_score')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),
                        Forms\Components\TextInput::make('letter_grade')
                            ->required()
                            ->maxLength(2),
                    ])->columns(2),

                Forms\Components\Section::make('Category Scores')
                    ->schema([
                        Forms\Components\TextInput::make('profile_completeness_score')
                            ->label('Profile Completeness')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),
                        Forms\Components\TextInput::make('online_presence_score')
                            ->label('Online Presence')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),
                        Forms\Components\TextInput::make('customer_engagement_score')
                            ->label('Customer Engagement')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),
                        Forms\Components\TextInput::make('menu_offerings_score')
                            ->label('Menu & Offerings')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),
                        Forms\Components\TextInput::make('mexican_authenticity_score')
                            ->label('Mexican Authenticity')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),
                        Forms\Components\TextInput::make('digital_readiness_score')
                            ->label('Digital Readiness')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),
                    ])->columns(3),

                Forms\Components\Section::make('Rankings')
                    ->schema([
                        Forms\Components\TextInput::make('area_rank')
                            ->numeric(),
                        Forms\Components\TextInput::make('area_total')
                            ->numeric(),
                        Forms\Components\TextInput::make('category_rank')
                            ->numeric(),
                        Forms\Components\TextInput::make('category_total')
                            ->numeric(),
                    ])->columns(4),

                Forms\Components\Section::make('Metadata')
                    ->schema([
                        Forms\Components\DateTimePicker::make('calculated_at'),
                        Forms\Components\DateTimePicker::make('expires_at'),
                        Forms\Components\TextInput::make('version')
                            ->numeric()
                            ->default(1),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('restaurant.name')
                    ->label('Restaurant')
                    ->searchable()
                    ->sortable()
                    ->description(fn (FamerScore $record): string =>
                        $record->restaurant?->city . ', ' . $record->restaurant?->state?->code
                    ),

                Tables\Columns\TextColumn::make('overall_score')
                    ->label('Score')
                    ->badge()
                    ->color(fn (FamerScore $record): string => match (true) {
                        $record->overall_score >= 90 => 'success',
                        $record->overall_score >= 70 => 'info',
                        $record->overall_score >= 50 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('letter_grade')
                    ->label('Grade')
                    ->badge()
                    ->color(fn (FamerScore $record): string => $record->grade_color)
                    ->sortable(),

                Tables\Columns\TextColumn::make('profile_completeness_score')
                    ->label('Profile')
                    ->suffix('/100')
                    ->color(fn (int $state): string => $state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('online_presence_score')
                    ->label('Presence')
                    ->suffix('/100')
                    ->color(fn (int $state): string => $state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('customer_engagement_score')
                    ->label('Engagement')
                    ->suffix('/100')
                    ->color(fn (int $state): string => $state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('menu_offerings_score')
                    ->label('Menu')
                    ->suffix('/100')
                    ->color(fn (int $state): string => $state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger'))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('mexican_authenticity_score')
                    ->label('Authenticity')
                    ->suffix('/100')
                    ->color(fn (int $state): string => $state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger'))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('digital_readiness_score')
                    ->label('Digital')
                    ->suffix('/100')
                    ->color(fn (int $state): string => $state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger'))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('area_rank')
                    ->label('Rank')
                    ->formatStateUsing(fn (FamerScore $record): string =>
                        $record->area_rank ? "#{$record->area_rank} of {$record->area_total}" : '-'
                    )
                    ->toggleable(),

                Tables\Columns\TextColumn::make('calculated_at')
                    ->label('Calculated')
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_expired')
                    ->label('Expired')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->getStateUsing(fn (FamerScore $record): bool => $record->isExpired())
                    ->toggleable(),
            ])
            ->defaultSort('overall_score', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('letter_grade')
                    ->options([
                        'A+' => 'A+',
                        'A' => 'A',
                        'A-' => 'A-',
                        'B+' => 'B+',
                        'B' => 'B',
                        'B-' => 'B-',
                        'C+' => 'C+',
                        'C' => 'C',
                        'C-' => 'C-',
                        'D+' => 'D+',
                        'D' => 'D',
                        'D-' => 'D-',
                        'F' => 'F',
                    ]),
                Tables\Filters\Filter::make('high_score')
                    ->label('High Scores (80+)')
                    ->query(fn (Builder $query) => $query->where('overall_score', '>=', 80)),
                Tables\Filters\Filter::make('low_score')
                    ->label('Low Scores (<50)')
                    ->query(fn (Builder $query) => $query->where('overall_score', '<', 50)),
                Tables\Filters\Filter::make('expired')
                    ->label('Expired Only')
                    ->query(fn (Builder $query) => $query->where('expires_at', '<', now())),
            ])
            ->actions([
                Tables\Actions\Action::make('recalculate')
                    ->label('Recalculate')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function (FamerScore $record) {
                        $service = app(FamerScoreService::class);
                        $service->calculateScore($record->restaurant, true);

                        Notification::make()
                            ->success()
                            ->title('Score Recalculated')
                            ->body("New score: {$record->fresh()->overall_score} ({$record->fresh()->letter_grade})")
                            ->send();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('recalculate_all')
                        ->label('Recalculate Selected')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $service = app(FamerScoreService::class);
                            $count = 0;
                            foreach ($records as $record) {
                                $service->calculateScore($record->restaurant, true);
                                $count++;
                            }

                            Notification::make()
                                ->success()
                                ->title('Scores Recalculated')
                                ->body("{$count} scores have been recalculated.")
                                ->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFamerScores::route('/'),
            'view' => Pages\ViewFamerScore::route('/{record}'),
            'edit' => Pages\EditFamerScore::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}

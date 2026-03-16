<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FanScoreResource\Pages;
use App\Models\FanScore;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FanScoreResource extends Resource
{
    protected static ?string $model = FanScore::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'Fans & Votantes';
    protected static ?string $navigationGroup = 'FAMER Awards';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Fan';
    protected static ?string $pluralModelLabel = 'Fans & Votantes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('restaurant_id')
                    ->relationship('restaurant', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('fan_level')
                    ->options([
                        'fan' => 'Fan (Bronce)',
                        'super_fan' => 'Super Fan (Plata)',
                        'fan_destacado' => 'Fan Destacado (Oro)',
                    ]),
                Forms\Components\TextInput::make('total_points')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('year')
                    ->numeric()
                    ->default(now()->year),
                Forms\Components\Toggle::make('badge_accepted'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Fan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('restaurant.name')
                    ->label('Restaurante')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\BadgeColumn::make('fan_level')
                    ->label('Nivel')
                    ->colors([
                        'warning' => 'fan',
                        'gray' => 'super_fan',
                        'success' => 'fan_destacado',
                    ])
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'fan' => 'Fan',
                        'super_fan' => 'Super Fan',
                        'fan_destacado' => 'Fan Destacado',
                        default => 'Sin nivel',
                    }),
                Tables\Columns\TextColumn::make('total_points')
                    ->label('Puntos')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('votes_count')
                    ->label('Votos')
                    ->sortable(),
                Tables\Columns\TextColumn::make('checkins_count')
                    ->label('Check-ins')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviews_count')
                    ->label('Reseñas')
                    ->sortable(),
                Tables\Columns\IconColumn::make('badge_accepted')
                    ->label('Badge')
                    ->boolean(),
                Tables\Columns\TextColumn::make('year')
                    ->label('Año')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Desde')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('total_points', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('fan_level')
                    ->label('Nivel')
                    ->options([
                        'fan' => 'Fan',
                        'super_fan' => 'Super Fan',
                        'fan_destacado' => 'Fan Destacado',
                    ]),
                Tables\Filters\SelectFilter::make('year')
                    ->label('Año')
                    ->options([
                        now()->year => now()->year,
                        now()->year - 1 => now()->year - 1,
                    ])
                    ->default(now()->year),
                Tables\Filters\Filter::make('has_badge')
                    ->label('Con insignia aceptada')
                    ->query(fn(Builder $query) => $query->where('badge_accepted', true)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFanScores::route('/'),
        ];
    }
}

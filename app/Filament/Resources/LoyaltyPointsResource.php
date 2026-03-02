<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoyaltyPointsResource\Pages;
use App\Filament\Resources\LoyaltyPointsResource\RelationManagers;
use App\Models\LoyaltyPoints;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoyaltyPointsResource extends Resource
{
    protected static ?string $model = LoyaltyPoints::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\TextInput::make('points')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('level')
                    ->required(),
                Forms\Components\TextInput::make('total_check_ins')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('total_reviews')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('total_referrals')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('points')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('level')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_check_ins')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_reviews')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_referrals')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListLoyaltyPoints::route('/'),
            'create' => Pages\CreateLoyaltyPoints::route('/create'),
            'edit' => Pages\EditLoyaltyPoints::route('/{record}/edit'),
        ];
    }
}

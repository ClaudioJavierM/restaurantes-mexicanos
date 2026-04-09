<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MexicanRegionResource\Pages;
use App\Models\MexicanRegion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MexicanRegionResource extends Resource
{
    protected static ?string $model = MexicanRegion::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationGroup = 'Clasificación';
    protected static ?string $navigationLabel = 'Regiones Mexicanas';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Región')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('icon')
                            ->label('Emoji/Icono')
                            ->maxLength(10),
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->helperText('Platillos típicos de esta región')
                            ->rows(3),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('icon')
                    ->label(''),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Platillos Típicos')
                    ->limit(50),
                Tables\Columns\TextColumn::make('restaurants_count')
                    ->label('Restaurantes')
                    ->counts('restaurants')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Activo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMexicanRegions::route('/'),
            'create' => Pages\CreateMexicanRegion::route('/create'),
            'edit' => Pages\EditMexicanRegion::route('/{record}/edit'),
        ];
    }
}

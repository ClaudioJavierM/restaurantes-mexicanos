<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdvertisementResource\Pages;
use App\Models\Advertisement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class AdvertisementResource extends Resource
{
    protected static ?string $model = Advertisement::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Publicidad';

    protected static ?string $modelLabel = 'Anuncio';

    protected static ?string $pluralModelLabel = 'Anuncios';

    protected static ?string $navigationGroup = 'Comunicaciones';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Anuncio')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('link_url')
                            ->label('URL del Anuncio')
                            ->url()
                            ->required()
                            ->placeholder('https://ejemplo.com')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('button_text')
                            ->label('Texto del Botón')
                            ->required()
                            ->default('Ver más')
                            ->maxLength(255),

                        Forms\Components\SpatieMediaLibraryFileUpload::make('image')
                            ->label('Imagen del Anuncio')
                            ->collection('image')
                            ->image()
                            ->required()
                            ->helperText('Tamaño recomendado: 300x250 px')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\Select::make('placement')
                            ->label('Ubicación')
                            ->options(Advertisement::getPlacements())
                            ->required()
                            ->default('sidebar'),

                        Forms\Components\Select::make('state_id')
                            ->label('Estado (opcional)')
                            ->relationship('state', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Deja vacío para mostrar en todos los estados'),

                        Forms\Components\TextInput::make('display_order')
                            ->label('Orden de Visualización')
                            ->numeric()
                            ->default(0)
                            ->helperText('Los anuncios con menor número se muestran primero'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true)
                            ->required(),

                        Forms\Components\DatePicker::make('starts_at')
                            ->label('Fecha de Inicio')
                            ->helperText('Opcional: Deja vacío para activar inmediatamente'),

                        Forms\Components\DatePicker::make('ends_at')
                            ->label('Fecha de Fin')
                            ->helperText('Opcional: Deja vacío para nunca expirar'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Estadísticas')
                    ->schema([
                        Forms\Components\TextInput::make('views_count')
                            ->label('Vistas')
                            ->numeric()
                            ->disabled(),

                        Forms\Components\TextInput::make('clicks_count')
                            ->label('Clicks')
                            ->numeric()
                            ->disabled(),
                    ])
                    ->columns(2)
                    ->visible(fn($record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagen')
                    ->circular()
                    ->getStateUsing(fn($record) => $record->getFirstMediaUrl('image')),

                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('placement')
                    ->label('Ubicación')
                    ->badge()
                    ->formatStateUsing(fn($state) => Advertisement::getPlacements()[$state] ?? $state)
                    ->sortable(),

                Tables\Columns\TextColumn::make('state.name')
                    ->label('Estado')
                    ->searchable()
                    ->sortable()
                    ->default('Todos')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('Vistas')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('clicks_count')
                    ->label('Clicks')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Inicia')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Termina')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Estado')
                    ->options([
                        1 => 'Activo',
                        0 => 'Inactivo',
                    ]),

                SelectFilter::make('placement')
                    ->label('Ubicación')
                    ->options(Advertisement::getPlacements())
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('display_order', 'asc');
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
            'index' => Pages\ListAdvertisements::route('/'),
            'create' => Pages\CreateAdvertisement::route('/create'),
            'edit' => Pages\EditAdvertisement::route('/{record}/edit'),
        ];
    }
}

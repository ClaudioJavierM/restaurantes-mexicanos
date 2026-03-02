<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\MyPhotosResource\Pages;
use App\Models\UserPhoto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class MyPhotosResource extends Resource
{
    protected static ?string $model = UserPhoto::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    
    protected static ?string $navigationLabel = 'Galería de Fotos';
    
    protected static ?string $modelLabel = 'Foto';
    
    protected static ?string $pluralModelLabel = 'Fotos';

    protected static ?string $navigationGroup = 'Contenido';
    
    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        $restaurantIds = auth()->user()->restaurants()->pluck('id');
        
        return parent::getEloquentQuery()
            ->whereIn('restaurant_id', $restaurantIds);
    }

    public static function form(Form $form): Form
    {
        $restaurant = auth()->user()->restaurants()->first();
        $maxPhotos = static::getMaxPhotos();
        $currentCount = static::getCurrentPhotoCount();
        
        return $form
            ->schema([
                Forms\Components\Section::make('Subir Foto')
                    ->description('Sube fotos de tu restaurante, platillos, interior o exterior')
                    ->schema([
                        Forms\Components\Hidden::make('restaurant_id')
                            ->default($restaurant?->id),

                        Forms\Components\Hidden::make('user_id')
                            ->default(auth()->id()),

                        Forms\Components\Hidden::make('status')
                            ->default('approved'), // Auto-approve owner photos

                        Forms\Components\FileUpload::make('photo_path')
                            ->label('Foto')
                            ->image()
                            ->disk('public')
                            ->directory('restaurant-photos')
                            ->imageEditor()
                            ->imageResizeTargetWidth('1200')
                            ->maxSize(5120)
                            ->required()
                            ->columnSpanFull()
                            ->helperText("Tamaño máximo: 5MB. Formatos: JPG, PNG, WebP. ({$currentCount}/{$maxPhotos} fotos usadas)"),

                        Forms\Components\Select::make('photo_type')
                            ->label('Tipo de Foto')
                            ->options([
                                'food' => '🍽️ Comida/Platillo',
                                'interior' => '🏠 Interior',
                                'exterior' => '🏢 Exterior/Fachada',
                                'menu' => '📋 Menú',
                                'drink' => '🍹 Bebida',
                                'other' => '📷 Otro',
                            ])
                            ->required()
                            ->default('food'),

                        Forms\Components\TextInput::make('caption')
                            ->label('Descripción (opcional)')
                            ->placeholder('Ej: Nuestros famosos tacos al pastor')
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo_path')
                    ->label('Foto')
                    ->disk('public')
                    ->width(100)
                    ->height(75),

                Tables\Columns\TextColumn::make('photo_type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'food' => '🍽️ Comida',
                        'interior' => '🏠 Interior',
                        'exterior' => '🏢 Exterior',
                        'menu' => '📋 Menú',
                        'drink' => '🍹 Bebida',
                        default => '📷 Otro',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('caption')
                    ->label('Descripción')
                    ->limit(30)
                    ->placeholder('Sin descripción'),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('👁️ Vistas')
                    ->sortable(),

                Tables\Columns\TextColumn::make('likes_count')
                    ->label('❤️ Likes')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Subida')
                    ->dateTime('d M, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('photo_type')
                    ->label('Tipo')
                    ->options([
                        'food' => 'Comida',
                        'interior' => 'Interior',
                        'exterior' => 'Exterior',
                        'menu' => 'Menú',
                        'drink' => 'Bebida',
                        'other' => 'Otro',
                    ]),
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyPhotos::route('/'),
            'create' => Pages\CreateMyPhoto::route('/create'),
            'edit' => Pages\EditMyPhoto::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $maxPhotos = static::getMaxPhotos();
        $currentCount = static::getCurrentPhotoCount();
        
        return $currentCount > 0 ? "{$currentCount}/{$maxPhotos}" : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $maxPhotos = static::getMaxPhotos();
        $currentCount = static::getCurrentPhotoCount();
        
        if ($currentCount >= $maxPhotos) {
            return 'danger';
        }
        
        return $currentCount > 0 ? 'success' : null;
    }

    public static function getMaxPhotos(): int
    {
        $restaurant = auth()->user()?->restaurants()->first();
        $plan = $restaurant?->subscription_tier ?? 'free';
        
        return match($plan) {
            'free' => 5,
            'premium' => 999,
            'elite' => 999, // Unlimited
            default => 5,
        };
    }

    public static function getCurrentPhotoCount(): int
    {
        $restaurantIds = auth()->user()?->restaurants()->pluck('id') ?? collect();
        
        return UserPhoto::whereIn('restaurant_id', $restaurantIds)->count();
    }

    public static function canCreate(): bool
    {
        return static::getCurrentPhotoCount() < static::getMaxPhotos();
    }
}

<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\MyMenuResource\Pages;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MyMenuResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Menú Digital';

    protected static ?string $modelLabel = 'Platillo';

    protected static ?string $pluralModelLabel = 'Platillos del Menú';

    protected static ?string $navigationGroup = 'Contenido';

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        $restaurant = auth()->user()?->restaurants()->first();
        return $restaurant && $restaurant->is_claimed;
    }

    public static function canAccess(): bool
    {
        $restaurant = auth()->user()?->restaurants()->first();
        return $restaurant && $restaurant->is_claimed;
    }

    public static function getNavigationBadge(): ?string
    {
        $restaurant = auth()->user()?->restaurants()->first();
        if ($restaurant && !in_array($restaurant->subscription_tier, ['premium', 'elite'])) {
            return 'PRO';
        }
        return null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canCreate(): bool
    {
        $restaurant = auth()->user()?->restaurants()->first();
        return $restaurant && $restaurant->is_claimed;
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $restaurantIds = $user->allAccessibleRestaurants()->pluck('id');

        return parent::getEloquentQuery()
            ->whereHas('category', function ($query) use ($restaurantIds) {
                $query->whereIn('restaurant_id', $restaurantIds);
            });
    }

    protected static function getAccessibleRestaurants()
    {
        $user = auth()->user();
        return Restaurant::where('user_id', $user->id)
            ->orWhereHas('teamMembers', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('status', 'active');
            })
            ->pluck('name', 'id');
    }

    protected static function getCategories()
    {
        $user = auth()->user();
        $restaurantIds = $user->allAccessibleRestaurants()->pluck('id');

        return MenuCategory::whereIn('restaurant_id', $restaurantIds)
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($cat) => [$cat->id => $cat->icon . ' ' . $cat->name]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Platillo')
                    ->schema([
                        Forms\Components\Select::make('menu_category_id')
                            ->label('Categoría')
                            ->options(fn () => static::getCategories())
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\Select::make('restaurant_id')
                                    ->label('Restaurante')
                                    ->options(fn () => static::getAccessibleRestaurants())
                                    ->required(),
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre de Categoría')
                                    ->required(),
                                Forms\Components\TextInput::make('icon')
                                    ->label('Emoji')
                                    ->placeholder('🌮')
                                    ->maxLength(10),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                return MenuCategory::create($data)->id;
                            }),

                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Platillo')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name_es')
                            ->label('Nombre en Español')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(2)
                            ->maxLength(500),

                        Forms\Components\FileUpload::make('image')
                            ->label('Imagen')
                            ->image()
                            ->disk('public')
                            ->directory('menu-items')
                            ->imageEditor()
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('600')
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Precio y Detalles')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Precio')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->minValue(0)
                            ->required(),

                        Forms\Components\TextInput::make('sale_price')
                            ->label('Precio de Oferta')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->minValue(0)
                            ->helperText('Deja vacío si no hay oferta'),

                        Forms\Components\TextInput::make('calories')
                            ->label('Calorías')
                            ->numeric()
                            ->suffix('kcal'),

                        Forms\Components\TextInput::make('prep_time')
                            ->label('Tiempo de Preparación')
                            ->numeric()
                            ->suffix('min'),

                        Forms\Components\Select::make('dish_type')
                            ->label('Tipo de Platillo')
                            ->options(MenuItem::getDishTypeOptions())
                            ->nullable()
                            ->searchable()
                            ->placeholder('Seleccionar tipo...')
                            ->columnSpanFull(),

                        Forms\Components\CheckboxList::make('dietary_tags')
                            ->label('Etiquetas Dietéticas')
                            ->options([
                                'vegetarian' => '🥬 Vegetariano',
                                'vegan' => '🌱 Vegano',
                                'gluten-free' => '🌾 Sin Gluten',
                                'spicy' => '🌶️ Picante',
                                'dairy-free' => '🥛 Sin Lácteos',
                                'keto' => '🥑 Keto',
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(4),

                Forms\Components\Section::make('Estado')
                    ->schema([
                        Forms\Components\Toggle::make('is_available')
                            ->label('Disponible')
                            ->default(true),

                        Forms\Components\Toggle::make('is_popular')
                            ->label('Popular')
                            ->helperText('Se destacará en el menú'),

                        Forms\Components\Toggle::make('is_new')
                            ->label('Nuevo')
                            ->helperText('Se mostrará con etiqueta "Nuevo"'),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder-food.svg')),

                Tables\Columns\TextColumn::make('name')
                    ->label('Platillo')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->category?->name),

                Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sale_price')
                    ->label('Oferta')
                    ->money('USD')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('dietary_tags')
                    ->label('Tags')
                    ->badge()
                    ->separator(','),

                Tables\Columns\TextColumn::make('dish_type')
                    ->label('Tipo')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => MenuItem::getDishTypeOptions()[$state] ?? $state)
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('is_popular')
                    ->label('⭐')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_available')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('menu_category_id')
                    ->label('Categoría')
                    ->options(fn () => static::getCategories())
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_available')
                    ->label('Disponible'),

                Tables\Filters\TernaryFilter::make('is_popular')
                    ->label('Popular'),
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
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyMenu::route('/'),
            'create' => Pages\CreateMyMenuItem::route('/create'),
            'edit' => Pages\EditMyMenuItem::route('/{record}/edit'),
            'upload' => Pages\UploadMenu::route('/upload'),
        ];
    }
}

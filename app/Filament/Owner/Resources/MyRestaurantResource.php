<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\MyRestaurantResource\Pages;
use App\Models\Restaurant;
use App\Models\State;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyRestaurantResource extends Resource
{
    protected static ?string $model = Restaurant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'Mis Restaurantes';

    protected static ?string $modelLabel = 'Restaurante';

    protected static ?string $pluralModelLabel = 'Mis Restaurantes';

    protected static ?string $navigationGroup = 'Mi Negocio';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        return parent::getEloquentQuery()
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('teamMembers', function ($q) use ($user) {
                        $q->where('user_id', $user->id)
                          ->where('status', 'active');
                    });
            });
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->count() ?: null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // INFORMACIÓN BÁSICA
                Forms\Components\Section::make('Información Básica')
                    ->description('Nombre, descripción y categoría de tu restaurante')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Restaurante')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('category_id')
                            ->label('Categoría')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->helperText('Describe tu restaurante, especialidades y qué lo hace único')
                            ->required()
                            ->rows(4)
                            ->maxLength(1000)
                            ->formatStateUsing(function ($state, $record = null) {
                                if ($record && method_exists($record, 'getTranslation')) {
                                    return $record->getTranslation('description', 'es', false)
                                        ?: $record->getTranslation('description', 'en', false)
                                        ?: (is_string($state) ? $state : '');
                                }
                                return is_string($state) ? $state : '';
                            })
                            ->dehydrateStateUsing(function ($state, $record = null) {
                                if ($record && method_exists($record, 'setTranslation')) {
                                    $record->setTranslation('description', 'es', $state);
                                    return $record->getTranslations('description');
                                }
                                return ['es' => $state];
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // IMÁGENES
                Forms\Components\Section::make('Imágenes')
                    ->description('Logo e imagen principal de tu restaurante')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo')
                            ->helperText('Cuadrado, mínimo 200x200px')
                            ->image()
                            ->disk('public')
                            ->directory('restaurants/logos')
                            ->imageEditor()
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('400')
                            ->maxSize(2048),

                        Forms\Components\FileUpload::make('image')
                            ->label('Imagen Principal / Portada')
                            ->helperText('Horizontal, mínimo 1200x675px')
                            ->image()
                            ->disk('public')
                            ->directory('restaurants')
                            ->imageEditor()
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('1200')
                            ->maxSize(5120),
                    ])
                    ->columns(2),

                // UBICACIÓN
                Forms\Components\Section::make('Ubicación')
                    ->description('Dirección y ubicación de tu restaurante')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('Dirección')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('city')
                            ->label('Ciudad')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\Select::make('state_id')
                            ->label('Estado')
                            ->options(State::where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        Forms\Components\TextInput::make('zip_code')
                            ->label('Código Postal')
                            ->maxLength(10),
                    ])
                    ->columns(3),

                // CONTACTO
                Forms\Components\Section::make('Contacto')
                    ->description('Teléfono, email y redes sociales')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->required()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('website')
                            ->label('Sitio Web')
                            ->url()
                            ->prefix('https://')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('facebook_url')
                            ->label('Facebook')
                            ->url()
                            ->prefix('https://')
                            ->placeholder('facebook.com/turestaurante')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('google_maps_url')
                            ->label('Google Maps')
                            ->url()
                            ->prefix('https://')
                            ->placeholder('maps.google.com/...')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // HORARIO
                Forms\Components\Section::make('Horario de Atención')
                    ->description('Días y horas que está abierto tu restaurante')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Forms\Components\KeyValue::make('hours')
                            ->label('')
                            ->keyLabel('Día')
                            ->valueLabel('Horario')
                            ->addButtonLabel('Agregar Día')
                            ->columnSpanFull()
                            ->formatStateUsing(function ($state) {
                                // Handle double-encoded JSON
                                if (is_string($state)) {
                                    $decoded = json_decode($state, true);
                                    $state = is_string($decoded) ? json_decode($decoded, true) : $decoded;
                                }
                                if (!is_array($state)) return [];
                                $dayNames = [
                                    'monday' => 'Lunes', 'tuesday' => 'Martes', 'wednesday' => 'Miércoles',
                                    'thursday' => 'Jueves', 'friday' => 'Viernes', 'saturday' => 'Sábado', 'sunday' => 'Domingo',
                                ];
                                $result = [];
                                foreach ($state as $day => $hours) {
                                    $dayLabel = $dayNames[strtolower($day)] ?? ucfirst($day);
                                    $hoursStr = is_array($hours) ? implode(', ', $hours) : $hours;
                                    $result[$dayLabel] = $hoursStr;
                                }
                                return $result;
                            })
                            ->dehydrateStateUsing(function ($state) {
                                if (!is_array($state)) return $state;
                                $dayKeys = [
                                    'Lunes' => 'monday', 'Martes' => 'tuesday', 'Miércoles' => 'wednesday',
                                    'Jueves' => 'thursday', 'Viernes' => 'friday', 'Sábado' => 'saturday', 'Domingo' => 'domingo',
                                ];
                                $result = [];
                                foreach ($state as $day => $hours) {
                                    $key = $dayKeys[$day] ?? strtolower($day);
                                    $result[$key] = is_string($hours) ? [$hours] : $hours;
                                }
                                return $result;
                            })
                            ->default([
                                'Lunes' => '10:00 AM - 10:00 PM',
                                'Martes' => '10:00 AM - 10:00 PM',
                                'Miércoles' => '10:00 AM - 10:00 PM',
                                'Jueves' => '10:00 AM - 10:00 PM',
                                'Viernes' => '10:00 AM - 11:00 PM',
                                'Sábado' => '9:00 AM - 11:00 PM',
                                'Domingo' => '9:00 AM - 10:00 PM',
                            ]),
                    ]),

                // CARACTERÍSTICAS DEL MENÚ
                Forms\Components\Section::make('Características del Menú')
                    ->description('Tipo de comida, nivel de picante y opciones especiales')
                    ->icon('heroicon-o-fire')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Select::make('price_range')
                            ->label('Rango de Precio')
                            ->options([
                                '$' => '$ (Económico - $5-15/persona)',
                                '$$' => '$$ (Moderado - $15-30/persona)',
                                '$$$' => '$$$ (Caro - $30-50/persona)',
                                '$$$$' => '$$$$ (Muy Caro - $50+/persona)',
                            ])
                            ->default('$$'),

                        Forms\Components\Select::make('spice_level')
                            ->label('Nivel de Picante')
                            ->options([
                                1 => '🌶️ Suave',
                                2 => '🌶️🌶️ Medio',
                                3 => '🌶️🌶️🌶️ Picante',
                                4 => '🌶️🌶️🌶️🌶️ Muy Picante',
                                5 => '🌶️🌶️🌶️🌶️🌶️ Extra Picante',
                            ]),

                        Forms\Components\Select::make('mexican_region')
                            ->label('Región Mexicana / Especialidad')
                            ->options(Restaurant::getMexicanRegions())
                            ->helperText('¿De qué región de México es tu cocina?'),

                        Forms\Components\CheckboxList::make('dietary_options')
                            ->label('Opciones Dietéticas')
                            ->options(Restaurant::getDietaryOptions())
                            ->columns(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                // AMBIENTE
                Forms\Components\Section::make('Ambiente y Características')
                    ->description('Tipo de ambiente y servicios especiales')
                    ->icon('heroicon-o-sparkles')
                    ->collapsible()
                    ->schema([
                        Forms\Components\CheckboxList::make('atmosphere')
                            ->label('Tipo de Ambiente')
                            ->options(Restaurant::getAtmosphereOptions())
                            ->columns(3)
                            ->columnSpanFull(),

                        Forms\Components\CheckboxList::make('special_features')
                            ->label('Características Especiales')
                            ->options(Restaurant::getSpecialFeatures())
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),

                // AUTENTICIDAD
                Forms\Components\Section::make('Autenticidad')
                    ->description('Certificaciones y sellos de autenticidad')
                    ->icon('heroicon-o-check-badge')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Toggle::make('chef_certified')
                            ->label('👨‍🍳 Chef Certificado')
                            ->helperText('El chef tiene certificación culinaria mexicana'),

                        Forms\Components\Toggle::make('traditional_recipes')
                            ->label('📖 Recetas Tradicionales')
                            ->helperText('Usamos recetas tradicionales mexicanas'),

                        Forms\Components\Toggle::make('imported_ingredients')
                            ->label('🇲🇽 Ingredientes de México')
                            ->helperText('Importamos ingredientes directamente de México'),
                    ])
                    ->columns(3),

                // SERVICIOS
                Forms\Components\Section::make('Servicios')
                    ->description('Reservaciones, pedidos en línea y más')
                    ->icon('heroicon-o-shopping-bag')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Toggle::make('accepts_reservations')
                            ->label('Acepta Reservaciones')
                            ->live(),

                        Forms\Components\Select::make('reservation_type')
                            ->label('Sistema de Reservaciones')
                            ->options([
                                'restaurante_famoso' => 'Usar sistema de FAMER',
                                'external' => 'Usar plataforma externa',
                                'none' => 'No acepta reservaciones',
                            ])
                            ->visible(fn (Forms\Get $get) => $get('accepts_reservations'))
                            ->default('none'),

                        Forms\Components\Select::make('reservation_platform')
                            ->label('Plataforma Externa')
                            ->options(Restaurant::getReservationPlatforms())
                            ->visible(fn (Forms\Get $get) => $get('reservation_type') === 'external'),

                        Forms\Components\TextInput::make('reservation_external_url')
                            ->label('URL de Reservaciones')
                            ->url()
                            ->visible(fn (Forms\Get $get) => $get('reservation_type') === 'external')
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('online_ordering')
                            ->label('Pedidos en Línea')
                            ->live(),

                        Forms\Components\TextInput::make('order_url')
                            ->label('URL de Pedidos')
                            ->url()
                            ->visible(fn (Forms\Get $get) => $get('online_ordering'))
                            ->placeholder('https://tu-sitio.com/pedidos'),

                        Forms\Components\Section::make('Plataformas de Delivery')
                            ->description('Agrega los links de tus plataformas de delivery para que los clientes puedan ordenar')
                            ->schema([
                                Forms\Components\TextInput::make('doordash_url')
                                    ->label('DoorDash')
                                    ->url()
                                    ->placeholder('https://doordash.com/store/tu-restaurante'),
                                Forms\Components\TextInput::make('ubereats_url')
                                    ->label('Uber Eats')
                                    ->url()
                                    ->placeholder('https://ubereats.com/store/tu-restaurante'),
                                Forms\Components\TextInput::make('grubhub_url')
                                    ->label('Grubhub')
                                    ->url()
                                    ->placeholder('https://grubhub.com/restaurant/tu-restaurante'),
                                Forms\Components\TextInput::make('postmates_url')
                                    ->label('Postmates')
                                    ->url()
                                    ->placeholder('https://postmates.com/store/tu-restaurante'),
                            ])
                            ->columns(2)
                            ->collapsible(),

                        Forms\Components\Section::make('Otras Plataformas')
                            ->description('Links a otras plataformas donde aparece tu restaurante')
                            ->schema([
                                Forms\Components\TextInput::make('tripadvisor_url')
                                    ->label('TripAdvisor')
                                    ->url()
                                    ->placeholder('https://tripadvisor.com/Restaurant-tu-restaurante'),
                                Forms\Components\TextInput::make('opentable_url')
                                    ->label('OpenTable')
                                    ->url()
                                    ->placeholder('https://opentable.com/tu-restaurante'),
                            ])
                            ->columns(2)
                            ->collapsible(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagen')
                    ->circular()
                    ->defaultImageUrl(url('/images/restaurant-placeholder.jpg')),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('city')
                    ->label('Ciudad')
                    ->searchable(),

                Tables\Columns\TextColumn::make('state.name')
                    ->label('Estado')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('my_role')
                    ->label('Mi Rol')
                    ->getStateUsing(function ($record) {
                        $user = auth()->user();
                        if ($record->user_id === $user->id) {
                            return 'Propietario';
                        }
                        $membership = $record->teamMembers()
                            ->where('user_id', $user->id)
                            ->where('status', 'active')
                            ->first();
                        return match($membership?->role) {
                            'owner' => 'Socio',
                            'manager' => 'Gerente',
                            'staff' => 'Staff',
                            default => '-',
                        };
                    })
                    ->colors([
                        'danger' => 'Propietario',
                        'warning' => fn ($state) => in_array($state, ['Socio', 'Gerente']),
                        'info' => 'Staff',
                    ]),

                Tables\Columns\TextColumn::make('average_rating')
                    ->label('Rating')
                    ->badge()
                    ->color(fn ($state) => $state >= 4 ? 'success' : ($state >= 3 ? 'warning' : 'danger'))
                    ->formatStateUsing(fn ($state) => number_format($state, 1) . ' ⭐'),

                Tables\Columns\TextColumn::make('total_reviews')
                    ->label('Reseñas')
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListMyRestaurants::route('/'),
            'edit' => Pages\EditMyRestaurant::route('/{record}/edit'),
            'view' => Pages\ViewMyRestaurant::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}

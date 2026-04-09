<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RestaurantResource\Pages;
use App\Filament\Resources\RestaurantResource\RelationManagers;
use App\Models\Restaurant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class RestaurantResource extends Resource
{
    protected static ?string $model = Restaurant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'Restaurantes';

    protected static ?string $navigationGroup = 'Plataforma';

    protected static ?string $modelLabel = 'Restaurante';

    protected static ?string $pluralModelLabel = 'Restaurantes';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Restaurante')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('category_id')
                            ->label('Categoría')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Contacto')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('website')
                            ->label('Sitio Web')
                            ->url()
                            ->maxLength(255),
                    ])->columns(3),

                Forms\Components\Section::make('Ubicación')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('Dirección')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('city')
                            ->label('Ciudad')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('state_id')
                            ->label('Estado')
                            ->relationship('state', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('zip_code')
                            ->label('Código Postal')
                            ->required()
                            ->maxLength(10),
                        Forms\Components\TextInput::make('latitude')
                            ->label('Latitud')
                            ->numeric()
                            ->step(0.000001)
                            ->dehydrateStateUsing(fn ($state) => $state ? round((float) $state, 8) : null)
                            ->helperText('Máx 8 decimales'),
                        Forms\Components\TextInput::make('longitude')
                            ->label('Longitud')
                            ->numeric()
                            ->step(0.000001)
                            ->dehydrateStateUsing(fn ($state) => $state ? round((float) $state, 8) : null)
                            ->helperText('Máx 8 decimales'),
                    ])->columns(3),

                Forms\Components\Section::make('Imágenes')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->collection('logo')
                            ->image()
                            ->maxSize(2048)
                            ->columnSpan(1),
                        SpatieMediaLibraryFileUpload::make('images')
                            ->label('Fotos del Restaurante')
                            ->collection('images')
                            ->image()
                            ->multiple()
                            ->maxFiles(10)
                            ->maxSize(2048)
                            ->columnSpan(2),
                        SpatieMediaLibraryFileUpload::make('menu')
                            ->label('Fotos del Menú')
                            ->collection('menu')
                            ->image()
                            ->multiple()
                            ->maxFiles(5)
                            ->maxSize(2048)
                            ->columnSpan(2),
                    ])->columns(3),

                Forms\Components\Section::make('Configuración')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pendiente',
                                'approved' => 'Aprobado',
                                'rejected' => 'Rechazado',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacado')
                            ->default(false),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                        Forms\Components\Select::make('user_id')
                            ->label('Dueño')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                    ])->columns(4),

                Forms\Components\Section::make('Sistema de Reservaciones')
                    ->description('Configure cómo los clientes pueden hacer reservaciones')
                    ->schema([
                        Forms\Components\Toggle::make('accepts_reservations')
                            ->label('Acepta Reservaciones')
                            ->default(false)
                            ->live()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('reservation_type')
                            ->label('Tipo de Sistema')
                            ->options([
                                'none' => 'Sin sistema de reservaciones',
                                'restaurante_famoso' => '🌟 Restaurante Famoso (sistema interno)',
                                'external' => '🔗 Plataforma externa (OpenTable, Yelp, etc.)',
                            ])
                            ->default('none')
                            ->live()
                            ->visible(fn (Forms\Get $get) => $get('accepts_reservations'))
                            ->columnSpanFull(),

                        // External Platform Settings
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Select::make('reservation_platform')
                                    ->label('Plataforma')
                                    ->options(Restaurant::getReservationPlatforms())
                                    ->required(),
                                Forms\Components\TextInput::make('reservation_external_url')
                                    ->label('URL de Reservaciones')
                                    ->url()
                                    ->required()
                                    ->placeholder('https://www.opentable.com/r/mi-restaurante')
                                    ->helperText('Enlace directo a la página de reservaciones'),
                            ])
                            ->columns(2)
                            ->visible(fn (Forms\Get $get) => $get('accepts_reservations') && $get('reservation_type') === 'external'),

                        // Internal Restaurante Famoso Settings
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Fieldset::make('Capacidad')
                                    ->schema([
                                        Forms\Components\TextInput::make('reservation_capacity_per_slot')
                                            ->label('Personas por horario')
                                            ->numeric()
                                            ->default(20)
                                            ->helperText('Máximo de personas que pueden reservar en el mismo horario'),
                                        Forms\Components\TextInput::make('reservation_tables_count')
                                            ->label('Número de mesas')
                                            ->numeric()
                                            ->helperText('Total de mesas disponibles para reservaciones'),
                                    ])->columns(2),

                                Forms\Components\KeyValue::make('reservation_settings')
                                    ->label('Configuración Avanzada')
                                    ->keyLabel('Opción')
                                    ->valueLabel('Valor')
                                    ->default([
                                        'min_party_size' => '1',
                                        'max_party_size' => '20',
                                        'advance_booking_days' => '30',
                                        'same_day_cutoff_hours' => '2',
                                        'time_slot_interval' => '30',
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->visible(fn (Forms\Get $get) => $get('accepts_reservations') && $get('reservation_type') === 'restaurante_famoso'),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Notificaciones de Reservaciones')
                    ->description('Configure cómo recibir notificaciones de nuevas reservaciones')
                    ->schema([
                        Forms\Components\TextInput::make('reservation_notification_email')
                            ->label('Email para notificaciones')
                            ->email()
                            ->placeholder('reservaciones@mirestaurante.com'),
                        Forms\Components\TextInput::make('reservation_notification_phone')
                            ->label('Teléfono para notificaciones')
                            ->tel()
                            ->placeholder('+1 555 123 4567'),
                        Forms\Components\Toggle::make('reservation_notify_email')
                            ->label('Notificar por Email')
                            ->default(true),
                        Forms\Components\Toggle::make('reservation_notify_sms')
                            ->label('Notificar por SMS')
                            ->default(false),
                        Forms\Components\Toggle::make('reservation_notify_whatsapp')
                            ->label('Notificar por WhatsApp')
                            ->default(false),
                    ])
                    ->columns(2)
                    ->visible(fn (Forms\Get $get) => $get('accepts_reservations') && $get('reservation_type') === 'restaurante_famoso')
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Notificaciones al Cliente')
                    ->description('Configure las notificaciones automáticas para los clientes')
                    ->schema([
                        Forms\Components\Toggle::make('reservation_send_confirmation')
                            ->label('Enviar confirmación automática')
                            ->helperText('El cliente recibe un email/SMS cuando su reservación es confirmada')
                            ->default(true),
                        Forms\Components\Toggle::make('reservation_send_reminder')
                            ->label('Enviar recordatorio')
                            ->helperText('El cliente recibe un recordatorio antes de su reservación')
                            ->default(true)
                            ->live(),
                        Forms\Components\Select::make('reservation_reminder_hours')
                            ->label('Horas antes del recordatorio')
                            ->options([
                                2 => '2 horas antes',
                                4 => '4 horas antes',
                                12 => '12 horas antes',
                                24 => '24 horas antes (1 día)',
                                48 => '48 horas antes (2 días)',
                            ])
                            ->default(24)
                            ->visible(fn (Forms\Get $get) => $get('reservation_send_reminder')),
                    ])
                    ->columns(3)
                    ->visible(fn (Forms\Get $get) => $get('accepts_reservations') && $get('reservation_type') === 'restaurante_famoso')
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('logo')
                    ->collection('logo')
                    ->circular()
                    ->size(50),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Ciudad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('state.name')
                    ->label('Estado')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                    }),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_claimed')
                    ->label('Reclamado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('average_rating')
                    ->label('Rating')
                    ->numeric(decimalPlaces: 1)
                    ->suffix(' ⭐')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_reviews')
                    ->label('Reviews')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make("country")
                    ->label("País")
                    ->options([
                        "US" => "🇺🇸 Estados Unidos",
                        "MX" => "🇲🇽 México",
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data["value"], function ($q, $country) {
                            return $q->whereHas("state", function ($sq) use ($country) {
                                $sq->where("country", $country);
                            });
                        });
                    })
                    ->default(null),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                    ]),
                Tables\Filters\SelectFilter::make('state')
                    ->relationship('state', 'name')
                    ->label('Estado'),
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('Categoría'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Destacado'),
                Tables\Filters\TernaryFilter::make('is_claimed')
                    ->label('Reclamado'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // If user is an owner, show their owned restaurants AND team restaurants
        $user = auth()->user();
        if ($user && $user->isOwner()) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('teamMembers', function ($q) use ($user) {
                      $q->where('user_id', $user->id)
                        ->where('status', 'active');
                  });
            });
        }

        return $query->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ReviewsRelationManager::class,
            RelationManagers\ReportsRelationManager::class,
            RelationManagers\EventsRelationManager::class,
            RelationManagers\CheckInsRelationManager::class,
            RelationManagers\CouponsRelationManager::class,
            RelationManagers\TeamMembersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListRestaurants::route("/"),
            "create" => Pages\CreateRestaurant::route("/create"),
            "view" => Pages\ViewRestaurant::route("/{record}"),
            "edit" => Pages\EditRestaurant::route("/{record}/edit"),
        ];
    }
}

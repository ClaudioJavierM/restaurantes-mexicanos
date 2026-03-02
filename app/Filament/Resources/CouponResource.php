<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Filament\Resources\CouponResource\RelationManagers;
use App\Models\Coupon;
use App\Models\Restaurant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Cupones';

    protected static ?string $modelLabel = 'Cupón';

    protected static ?string $pluralModelLabel = 'Cupones';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        $user = auth()->user();
        $restaurantQuery = Restaurant::query();

        // If user is owner, only show their restaurants
        if ($user && $user->isOwner()) {
            $restaurantQuery->where('user_id', $user->id);
        }

        return $form
            ->schema([
                Forms\Components\Section::make('Información del Cupón')
                    ->schema([
                        Forms\Components\Select::make('restaurant_id')
                            ->label('Restaurante')
                            ->options($restaurantQuery->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('code')
                            ->label('Código del Cupón')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->alphaDash()
                            ->uppercase()
                            ->hint('Solo letras, números y guiones'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                    ])->columns(3),

                Forms\Components\Section::make('Títulos y Descripciones')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título (Español)')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('title_en')
                            ->label('Título (Inglés)')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción (Español)')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description_en')
                            ->label('Descripción (Inglés)')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Configuración de Descuento')
                    ->schema([
                        Forms\Components\Select::make('discount_type')
                            ->label('Tipo de Descuento')
                            ->options([
                                'percentage' => 'Porcentaje (%)',
                                'fixed_amount' => 'Cantidad Fija ($)',
                            ])
                            ->default('percentage')
                            ->required()
                            ->live(),
                        Forms\Components\TextInput::make('discount_value')
                            ->label('Valor del Descuento')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->suffix(fn ($get) => $get('discount_type') === 'percentage' ? '%' : '$'),
                        Forms\Components\TextInput::make('minimum_purchase')
                            ->label('Compra Mínima')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$'),
                        Forms\Components\TextInput::make('maximum_discount')
                            ->label('Descuento Máximo')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->hint('Solo para porcentajes'),
                    ])->columns(4),

                Forms\Components\Section::make('Validez y Uso')
                    ->schema([
                        Forms\Components\DatePicker::make('valid_from')
                            ->label('Válido Desde')
                            ->default(now()),
                        Forms\Components\DatePicker::make('valid_until')
                            ->label('Válido Hasta')
                            ->after('valid_from'),
                        Forms\Components\TextInput::make('usage_limit')
                            ->label('Límite de Uso Total')
                            ->numeric()
                            ->minValue(0)
                            ->hint('Dejar vacío para ilimitado'),
                        Forms\Components\TextInput::make('usage_limit_per_user')
                            ->label('Límite por Usuario')
                            ->numeric()
                            ->minValue(0)
                            ->hint('Dejar vacío para ilimitado'),
                    ])->columns(4),

                Forms\Components\Section::make('Restricciones')
                    ->schema([
                        Forms\Components\Select::make('applicable_days')
                            ->label('Días Aplicables')
                            ->multiple()
                            ->options([
                                'monday' => 'Lunes',
                                'tuesday' => 'Martes',
                                'wednesday' => 'Miércoles',
                                'thursday' => 'Jueves',
                                'friday' => 'Viernes',
                                'saturday' => 'Sábado',
                                'sunday' => 'Domingo',
                            ])
                            ->hint('Dejar vacío para todos los días')
                            ->columnSpan(2),
                        Forms\Components\TimePicker::make('applicable_time_start')
                            ->label('Hora de Inicio'),
                        Forms\Components\TimePicker::make('applicable_time_end')
                            ->label('Hora de Fin'),
                        Forms\Components\Toggle::make('applicable_dine_in')
                            ->label('Comer en el Lugar')
                            ->default(true),
                        Forms\Components\Toggle::make('applicable_takeout')
                            ->label('Para Llevar')
                            ->default(true),
                        Forms\Components\Toggle::make('applicable_delivery')
                            ->label('Entrega a Domicilio')
                            ->default(true),
                    ])->columns(4),

                Forms\Components\Section::make('Características Adicionales')
                    ->schema([
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacado')
                            ->default(false),
                        Forms\Components\Toggle::make('requires_subscription')
                            ->label('Requiere Suscripción Premium')
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('Términos y Condiciones')
                    ->schema([
                        Forms\Components\Textarea::make('terms')
                            ->label('Términos (Español)')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('terms_en')
                            ->label('Términos (Inglés)')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('restaurant.name')
                    ->label('Restaurante')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('discount_type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => $state === 'percentage' ? 'Porcentaje' : 'Cantidad Fija')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'percentage' ? 'warning' : 'info'),
                Tables\Columns\TextColumn::make('discount_value')
                    ->label('Descuento')
                    ->formatStateUsing(fn ($record): string =>
                        $record->discount_type === 'percentage'
                            ? $record->discount_value . '%'
                            : '$' . number_format($record->discount_value, 2)
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('valid_from')
                    ->label('Válido Desde')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('valid_until')
                    ->label('Válido Hasta')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('usage_count')
                    ->label('Usos')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Vistas')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('restaurant')
                    ->relationship('restaurant', 'name')
                    ->label('Restaurante')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('discount_type')
                    ->options([
                        'percentage' => 'Porcentaje',
                        'fixed_amount' => 'Cantidad Fija',
                    ])
                    ->label('Tipo de Descuento'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Activo'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Destacado'),
                Tables\Filters\Filter::make('valid')
                    ->query(fn (Builder $query): Builder =>
                        $query->where(function ($q) {
                            $q->where('valid_from', '<=', now())
                              ->orWhereNull('valid_from');
                        })
                        ->where(function ($q) {
                            $q->where('valid_until', '>=', now())
                              ->orWhereNull('valid_until');
                        })
                    )
                    ->label('Cupones Válidos'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_stats')
                    ->label('Ver Estadísticas')
                    ->icon('heroicon-o-chart-bar')
                    ->modalContent(fn (Coupon $record): \Illuminate\View\View => view(
                        'filament.resources.coupon.stats',
                        ['record' => $record],
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),
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

        // If user is an owner, only show coupons from their restaurants
        $user = auth()->user();
        if ($user && $user->isOwner()) {
            $query->whereHas('restaurant', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        return $query->withoutGlobalScopes([
            SoftDeletingScope::class,
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
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}

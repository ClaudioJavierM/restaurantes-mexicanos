<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\MenuAbTestsResource\Pages;
use App\Models\MenuAbTest;
use App\Models\MenuItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MenuAbTestsResource extends Resource
{
    protected static ?string $model = MenuAbTest::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationLabel = 'Pruebas A/B Menú';
    protected static ?string $modelLabel = 'Prueba A/B';
    protected static ?string $pluralModelLabel = 'Pruebas A/B del Menú';
    protected static ?string $navigationGroup = 'Contenido';
    protected static ?int $navigationSort = 5;

    public static function getEloquentQuery(): Builder
    {
        $restaurantIds = auth()->user()->allAccessibleRestaurants()->pluck('id');
        return parent::getEloquentQuery()->whereIn('restaurant_id', $restaurantIds)->latest();
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        $restaurant = $user->allAccessibleRestaurants()->first();
        return $restaurant && $restaurant->is_claimed;
    }

    public static function canAccess(): bool
    {
        return static::shouldRegisterNavigation();
    }

    public static function getNavigationBadge(): ?string
    {
        $restaurant = auth()->user()?->allAccessibleRestaurants()->first();
        if (!$restaurant || $restaurant->subscription_tier !== 'elite') {
            return 'ELITE';
        }
        return null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $restaurant = auth()->user()?->allAccessibleRestaurants()->first();
        if (!$restaurant || $restaurant->subscription_tier !== 'elite') {
            return 'violet';
        }
        return 'success';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información del Test')
                ->schema([
                    Forms\Components\TextInput::make('test_name')
                        ->label('Nombre del test')
                        ->required()
                        ->placeholder('Ej: Precio tacos vs precio normal'),

                    Forms\Components\Select::make('menu_item_id')
                        ->label('Platillo base (opcional)')
                        ->options(function () {
                            $restaurant = auth()->user()->allAccessibleRestaurants()->first();
                            return $restaurant
                                ? MenuItem::where('restaurant_id', $restaurant->id)
                                    ->where('is_available', true)
                                    ->pluck('name', 'id')
                                : [];
                        })
                        ->searchable()
                        ->nullable(),
                ])->columns(2),

            Forms\Components\Section::make('Variante A — Control')
                ->description('La versión actual (punto de referencia)')
                ->schema([
                    Forms\Components\TextInput::make('variant_a_name')
                        ->label('Nombre')
                        ->required(),
                    Forms\Components\Textarea::make('variant_a_description')
                        ->label('Descripción')
                        ->rows(2),
                    Forms\Components\TextInput::make('variant_a_price')
                        ->label('Precio (USD)')
                        ->numeric()
                        ->prefix('$'),
                ])->columns(3),

            Forms\Components\Section::make('Variante B — Challenger')
                ->description('La versión a probar')
                ->schema([
                    Forms\Components\TextInput::make('variant_b_name')
                        ->label('Nombre')
                        ->required(),
                    Forms\Components\Textarea::make('variant_b_description')
                        ->label('Descripción')
                        ->rows(2),
                    Forms\Components\TextInput::make('variant_b_price')
                        ->label('Precio (USD)')
                        ->numeric()
                        ->prefix('$'),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('test_name')
                    ->label('Test')
                    ->searchable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'draft' => 'Borrador', 'active' => 'Activo', 'completed' => 'Completado',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match($state) {
                        'draft' => 'gray', 'active' => 'success', 'completed' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('variant_a_name')
                    ->label('A')
                    ->description(fn (MenuAbTest $r): string => '$' . number_format((float)$r->variant_a_price, 2)),

                Tables\Columns\TextColumn::make('variant_b_name')
                    ->label('B')
                    ->description(fn (MenuAbTest $r): string => '$' . number_format((float)$r->variant_b_price, 2)),

                Tables\Columns\TextColumn::make('orders_a')
                    ->label('Órdenes A')
                    ->suffix(fn (MenuAbTest $r): string => ' (' . $r->conversionRateA() . '%)'),

                Tables\Columns\TextColumn::make('orders_b')
                    ->label('Órdenes B')
                    ->suffix(fn (MenuAbTest $r): string => ' (' . $r->conversionRateB() . '%)'),

                Tables\Columns\TextColumn::make('winner')
                    ->label('Ganador')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match($state) {
                        'a' => '🏆 Variante A', 'b' => '🏆 Variante B', 'tie' => '🤝 Empate',
                        default => '—',
                    })
                    ->color(fn (?string $state): string => match($state) {
                        'a', 'b' => 'success', 'tie' => 'warning', default => 'gray',
                    })
                    ->placeholder('—'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nuevo Test')
                    ->visible(fn (): bool => (bool) (auth()->user()?->allAccessibleRestaurants()->first()?->subscription_tier === 'elite')),
            ])
            ->actions([
                Tables\Actions\Action::make('activate')
                    ->label('Activar')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn (MenuAbTest $r): bool => $r->status === 'draft')
                    ->action(function (MenuAbTest $record): void {
                        $record->update(['status' => 'active', 'started_at' => now()]);
                        Notification::make()->title('Test activado')->success()->send();
                    }),

                Tables\Actions\Action::make('complete')
                    ->label('Finalizar')
                    ->icon('heroicon-o-stop')
                    ->color('warning')
                    ->visible(fn (MenuAbTest $r): bool => $r->status === 'active')
                    ->form([
                        Forms\Components\Select::make('winner')
                            ->label('¿Quién ganó?')
                            ->options(['a' => 'Variante A', 'b' => 'Variante B', 'tie' => 'Empate'])
                            ->required(),
                    ])
                    ->action(function (MenuAbTest $record, array $data): void {
                        $record->update([
                            'status'   => 'completed',
                            'winner'   => $data['winner'],
                            'ended_at' => now(),
                        ]);
                        Notification::make()->title('Test completado')->success()->send();
                    }),

                Tables\Actions\Action::make('record_order')
                    ->label('Registrar Orden')
                    ->icon('heroicon-o-plus-circle')
                    ->color('gray')
                    ->visible(fn (MenuAbTest $r): bool => $r->status === 'active')
                    ->form([
                        Forms\Components\Select::make('variant')
                            ->label('¿Qué variante ordenó el cliente?')
                            ->options(['a' => 'Variante A', 'b' => 'Variante B'])
                            ->required(),
                    ])
                    ->action(function (MenuAbTest $record, array $data): void {
                        if ($data['variant'] === 'a') {
                            $record->increment('orders_a');
                            $record->increment('views_a');
                        } else {
                            $record->increment('orders_b');
                            $record->increment('views_b');
                        }
                        Notification::make()->title('Orden registrada')->success()->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading(function (): string {
                $restaurant = auth()->user()?->allAccessibleRestaurants()->first();
                return ($restaurant && $restaurant->subscription_tier === 'elite')
                    ? 'Sin pruebas A/B activas' : '🔒 Función Elite';
            })
            ->emptyStateDescription(function (): string {
                $restaurant = auth()->user()?->allAccessibleRestaurants()->first();
                return ($restaurant && $restaurant->subscription_tier === 'elite')
                    ? 'Crea tu primera prueba A/B para optimizar tu menú.'
                    : 'Las pruebas A/B del menú son una función exclusiva del plan Elite.';
            })
            ->emptyStateActions(function (): array {
                $restaurant = auth()->user()?->allAccessibleRestaurants()->first();
                if (!$restaurant || $restaurant->subscription_tier !== 'elite') {
                    return [
                        Tables\Actions\Action::make('upgrade')
                            ->label('Ver plan Elite')
                            ->url(\App\Filament\Owner\Pages\MySubscription::getUrl())
                            ->color('violet')
                            ->icon('heroicon-o-arrow-up-circle'),
                    ];
                }
                return [];
            });
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMenuAbTests::route('/'),
            'create' => Pages\CreateMenuAbTest::route('/create'),
            'edit'   => Pages\EditMenuAbTest::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\InventoryResource\Pages;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InventoryResource extends Resource
{
    protected static ?string $model = InventoryItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel = 'Inventario';
    protected static ?string $modelLabel = 'Artículo';
    protected static ?string $pluralModelLabel = 'Inventario';
    protected static ?string $navigationGroup = 'Mi Negocio';
    protected static ?int $navigationSort = 12;

    public static function getEloquentQuery(): Builder
    {
        $restaurantIds = auth()->user()->allAccessibleRestaurants()->pluck('id');
        return parent::getEloquentQuery()->whereIn('restaurant_id', $restaurantIds);
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
        $restaurantIds = auth()->user()->allAccessibleRestaurants()->pluck('id');
        $count = InventoryItem::whereIn('restaurant_id', $restaurantIds)->lowStock()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $restaurant = auth()->user()?->allAccessibleRestaurants()->first();
        if (!$restaurant || $restaurant->subscription_tier !== 'elite') {
            return 'violet';
        }
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Artículo')
                ->required(),

            Forms\Components\Select::make('category')
                ->label('Categoría')
                ->options(collect(InventoryItem::$categories)->map(fn ($v) => $v)->toArray())
                ->required(),

            Forms\Components\TextInput::make('unit')
                ->label('Unidad')
                ->placeholder('kg, L, pcs, lbs...')
                ->required(),

            Forms\Components\TextInput::make('current_stock')
                ->label('Stock actual')
                ->numeric()
                ->minValue(0)
                ->required(),

            Forms\Components\TextInput::make('min_stock')
                ->label('Stock mínimo')
                ->numeric()
                ->minValue(0)
                ->helperText('Se mostrará alerta si el stock cae por debajo de este valor'),

            Forms\Components\TextInput::make('cost_per_unit')
                ->label('Costo por unidad (USD)')
                ->numeric()
                ->prefix('$'),

            Forms\Components\Textarea::make('notes')
                ->label('Notas')
                ->rows(2)
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category')
                    ->label('Categoría')
                    ->formatStateUsing(fn (string $state): string =>
                        InventoryItem::$categories[$state] ?? $state)
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Artículo')
                    ->searchable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('current_stock')
                    ->label('Stock')
                    ->formatStateUsing(fn (InventoryItem $r): string =>
                        number_format((float)$r->current_stock, 1) . ' ' . $r->unit)
                    ->color(fn (InventoryItem $r): string =>
                        $r->isLowStock() ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('min_stock')
                    ->label('Mínimo')
                    ->formatStateUsing(fn (InventoryItem $r): string =>
                        number_format((float)$r->min_stock, 1) . ' ' . $r->unit)
                    ->color('gray'),

                Tables\Columns\TextColumn::make('cost_per_unit')
                    ->label('Costo/u')
                    ->money('USD')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última actualización')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('category')
            ->filters([
                Tables\Filters\Filter::make('low_stock')
                    ->label('Solo stock bajo')
                    ->query(fn (Builder $query) => $query->lowStock()),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Agregar Artículo')
                    ->mutateFormDataUsing(function (array $data): array {
                        $restaurant = auth()->user()->allAccessibleRestaurants()->first();
                        $data['restaurant_id'] = $restaurant->id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('add_stock')
                    ->label('Entrada')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->minValue(0.001)
                            ->required(),
                        Forms\Components\TextInput::make('unit_cost')
                            ->label('Costo unitario (USD)')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->rows(1),
                    ])
                    ->action(function (InventoryItem $record, array $data): void {
                        $restaurant = auth()->user()->allAccessibleRestaurants()->first();
                        InventoryMovement::create([
                            'inventory_item_id' => $record->id,
                            'restaurant_id'     => $restaurant->id,
                            'type'              => 'in',
                            'quantity'          => $data['quantity'],
                            'unit_cost'         => $data['unit_cost'] ?? null,
                            'notes'             => $data['notes'] ?? null,
                            'user_id'           => auth()->id(),
                        ]);
                        $record->increment('current_stock', $data['quantity']);
                        Notification::make()
                            ->title('Stock actualizado: +' . $data['quantity'] . ' ' . $record->unit)
                            ->success()->send();
                    }),

                Tables\Actions\Action::make('remove_stock')
                    ->label('Salida')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('warning')
                    ->form([
                        Forms\Components\TextInput::make('quantity')
                            ->label('Cantidad a retirar')
                            ->numeric()
                            ->minValue(0.001)
                            ->required(),
                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options(['out' => 'Salida (uso)', 'waste' => 'Merma'])
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Motivo')
                            ->rows(1),
                    ])
                    ->action(function (InventoryItem $record, array $data): void {
                        $restaurant = auth()->user()->allAccessibleRestaurants()->first();
                        $qty = min((float)$data['quantity'], (float)$record->current_stock);
                        InventoryMovement::create([
                            'inventory_item_id' => $record->id,
                            'restaurant_id'     => $restaurant->id,
                            'type'              => $data['type'],
                            'quantity'          => $qty,
                            'notes'             => $data['notes'] ?? null,
                            'user_id'           => auth()->id(),
                        ]);
                        $record->decrement('current_stock', $qty);
                        Notification::make()
                            ->title('Stock actualizado: -' . $qty . ' ' . $record->unit)
                            ->success()->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([])
            ->emptyStateHeading(function (): string {
                $restaurant = auth()->user()?->allAccessibleRestaurants()->first();
                return ($restaurant && $restaurant->subscription_tier === 'elite')
                    ? 'Sin artículos en inventario' : '🔒 Función Elite';
            })
            ->emptyStateDescription(function (): string {
                $restaurant = auth()->user()?->allAccessibleRestaurants()->first();
                return ($restaurant && $restaurant->subscription_tier === 'elite')
                    ? 'Agrega tu primer artículo para comenzar a controlar el inventario.'
                    : 'El control de inventario es una función exclusiva del plan Elite.';
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
            'index'  => Pages\ListInventoryItems::route('/'),
            'create' => Pages\CreateInventoryItem::route('/create'),
            'edit'   => Pages\EditInventoryItem::route('/{record}/edit'),
        ];
    }
}

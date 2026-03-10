<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\PickupOrdersResource\Pages;
use App\Models\PickupOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PickupOrdersResource extends Resource
{
    protected static ?string $model = PickupOrder::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Pedidos Pickup';
    protected static ?string $modelLabel = 'Pedido';
    protected static ?string $pluralModelLabel = 'Pedidos Pickup';
    protected static ?string $navigationGroup = 'Mi Negocio';
    protected static ?int $navigationSort = 9;

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

    public static function canCreate(): bool
    {
        $restaurant = auth()->user()?->restaurants()->first();
        return $restaurant && in_array($restaurant->subscription_tier, ["premium", "elite"]);
    }

    public static function getNavigationBadge(): ?string
    {
        $restaurant = auth()->user()?->restaurants()->first();
        if ($restaurant && !in_array($restaurant->subscription_tier, ["premium", "elite"])) {
            return "PRO";
        }
        return null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return "warning";
    }

    public static function getEloquentQuery(): Builder
    {
        $restaurantIds = auth()->user()->restaurants()->pluck('id');
        return parent::getEloquentQuery()->whereIn('restaurant_id', $restaurantIds);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Orden')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Cliente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer_phone')
                    ->label('Telefono'),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('usd'),
                Tables\Columns\TextColumn::make('pickup_time')
                    ->label('Hora Pickup')
                    ->dateTime('d M, h:i A'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'confirmed',
                        'primary' => 'preparing',
                        'success' => 'ready',
                        'gray' => 'picked_up',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state) => PickupOrder::STATUSES[$state] ?? $state),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(PickupOrder::STATUSES),
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->label('Confirmar')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (PickupOrder $record) => $record->status === 'pending')
                    ->action(fn (PickupOrder $record) => $record->updateStatus('confirmed')),
                Tables\Actions\Action::make('prepare')
                    ->label('Preparando')
                    ->icon('heroicon-o-fire')
                    ->color('warning')
                    ->visible(fn (PickupOrder $record) => $record->status === 'confirmed')
                    ->action(fn (PickupOrder $record) => $record->updateStatus('preparing')),
                Tables\Actions\Action::make('ready')
                    ->label('Listo')
                    ->icon('heroicon-o-bell')
                    ->color('info')
                    ->visible(fn (PickupOrder $record) => $record->status === 'preparing')
                    ->action(fn (PickupOrder $record) => $record->updateStatus('ready')),
                Tables\Actions\Action::make('pickedup')
                    ->label('Entregado')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (PickupOrder $record) => $record->status === 'ready')
                    ->action(fn (PickupOrder $record) => $record->updateStatus('picked_up')),
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPickupOrders::route('/'),
        ];
    }


}

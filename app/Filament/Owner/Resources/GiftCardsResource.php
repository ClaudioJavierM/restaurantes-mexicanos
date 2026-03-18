<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\GiftCardsResource\Pages;
use App\Models\GiftCard;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GiftCardsResource extends Resource
{
    protected static ?string $model = GiftCard::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationLabel = 'Tarjetas de Regalo';
    protected static ?string $modelLabel = 'Tarjeta de Regalo';
    protected static ?string $pluralModelLabel = 'Tarjetas de Regalo';
    protected static ?string $navigationGroup = 'Mi Negocio';
    protected static ?int $navigationSort = 8;

    public static function getEloquentQuery(): Builder
    {
        $restaurantIds = auth()->user()->allAccessibleRestaurants()->pluck('id');

        return parent::getEloquentQuery()
            ->whereIn('restaurant_id', $restaurantIds)
            ->latest();
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
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->copyable()
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('purchaser_name')
                    ->label('Comprador')
                    ->searchable(),

                Tables\Columns\TextColumn::make('recipient_name')
                    ->label('Destinatario')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('initial_amount')
                    ->label('Valor Original')
                    ->money('USD'),

                Tables\Columns\TextColumn::make('balance')
                    ->label('Saldo')
                    ->money('USD')
                    ->color(fn (GiftCard $record): string =>
                        $record->balance > 0 ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'active' => 'Activa', 'used' => 'Usada',
                        'expired' => 'Vencida', 'cancelled' => 'Cancelada',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match($state) {
                        'active' => 'success', 'used' => 'gray',
                        'expired' => 'warning', 'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Vence')
                    ->date('d M, Y')
                    ->placeholder('Sin vencimiento'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Emitida')
                    ->dateTime('d M, Y')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('create_gift_card')
                    ->label('Emitir Tarjeta')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->visible(fn (): bool => (bool) (auth()->user()?->allAccessibleRestaurants()->first()?->subscription_tier === 'elite'))
                    ->form([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('purchaser_name')
                                ->label('Nombre del comprador')
                                ->required(),
                            Forms\Components\TextInput::make('purchaser_email')
                                ->label('Email del comprador')
                                ->email()
                                ->required(),
                            Forms\Components\TextInput::make('amount')
                                ->label('Monto (USD)')
                                ->numeric()
                                ->prefix('$')
                                ->required()
                                ->minValue(10),
                            Forms\Components\DatePicker::make('expires_at')
                                ->label('Fecha de vencimiento')
                                ->minDate(now()->addWeek()),
                            Forms\Components\TextInput::make('recipient_name')
                                ->label('Nombre del destinatario'),
                            Forms\Components\TextInput::make('recipient_email')
                                ->label('Email del destinatario')
                                ->email(),
                        ]),
                        Forms\Components\Textarea::make('message')
                            ->label('Mensaje personal')
                            ->rows(2)
                            ->maxLength(300),
                    ])
                    ->action(function (array $data): void {
                        $restaurant = auth()->user()->allAccessibleRestaurants()->first();

                        GiftCard::create([
                            'restaurant_id'    => $restaurant->id,
                            'code'             => GiftCard::generateCode(),
                            'initial_amount'   => $data['amount'],
                            'balance'          => $data['amount'],
                            'purchaser_name'   => $data['purchaser_name'],
                            'purchaser_email'  => $data['purchaser_email'],
                            'recipient_name'   => $data['recipient_name'] ?? null,
                            'recipient_email'  => $data['recipient_email'] ?? null,
                            'message'          => $data['message'] ?? null,
                            'expires_at'       => $data['expires_at'] ?? null,
                            'status'           => 'active',
                        ]);

                        Notification::make()->title('Tarjeta de regalo emitida')->success()->send();
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('redeem')
                    ->label('Registrar Uso')
                    ->icon('heroicon-o-check-badge')
                    ->color('warning')
                    ->visible(fn (GiftCard $record): bool => $record->status === 'active' && $record->balance > 0)
                    ->form([
                        Forms\Components\TextInput::make('amount_used')
                            ->label('Monto a descontar (USD)')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                    ])
                    ->action(function (GiftCard $record, array $data): void {
                        $used = min((float)$data['amount_used'], $record->balance);
                        $newBalance = $record->balance - $used;

                        $record->update([
                            'balance' => $newBalance,
                            'status'  => $newBalance <= 0 ? 'used' : 'active',
                        ]);

                        Notification::make()
                            ->title('Uso registrado — Saldo restante: $' . number_format($newBalance, 2))
                            ->success()->send();
                    }),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading(function (): string {
                $restaurant = auth()->user()?->allAccessibleRestaurants()->first();
                return ($restaurant && $restaurant->subscription_tier === 'elite')
                    ? 'Sin tarjetas emitidas' : '🔒 Función Elite';
            })
            ->emptyStateDescription(function (): string {
                $restaurant = auth()->user()?->allAccessibleRestaurants()->first();
                return ($restaurant && $restaurant->subscription_tier === 'elite')
                    ? 'Emite tu primera tarjeta de regalo digital usando el botón de arriba.'
                    : 'Las tarjetas de regalo digitales son una función exclusiva del plan Elite.';
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
            'index' => Pages\ListGiftCards::route('/'),
        ];
    }
}

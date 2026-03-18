<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\CateringRequestsResource\Pages;
use App\Models\CateringRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CateringRequestsResource extends Resource
{
    protected static ?string $model = CateringRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-cake';
    protected static ?string $navigationLabel = 'Solicitudes de Catering';
    protected static ?string $modelLabel = 'Solicitud';
    protected static ?string $pluralModelLabel = 'Solicitudes de Catering';
    protected static ?string $navigationGroup = 'Mi Negocio';
    protected static ?int $navigationSort = 5;

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
        if (!$restaurant || !in_array($restaurant->subscription_tier, ['premium', 'elite'])) {
            return 'PRO';
        }
        $restaurantIds = auth()->user()->allAccessibleRestaurants()->pluck('id');
        $count = \App\Models\CateringRequest::whereIn('restaurant_id', $restaurantIds)
            ->where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $restaurant = auth()->user()?->allAccessibleRestaurants()->first();
        if (!$restaurant || !in_array($restaurant->subscription_tier, ['premium', 'elite'])) {
            return 'warning';
        }
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contact_name')
                    ->label('Cliente')
                    ->searchable(),

                Tables\Columns\TextColumn::make('event_type')
                    ->label('Tipo Evento')
                    ->formatStateUsing(fn (string $state): string => CateringRequest::$eventTypes[$state] ?? ucfirst($state))
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('event_date')
                    ->label('Fecha Evento')
                    ->date('d M, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('guest_count')
                    ->label('Invitados')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('budget')
                    ->label('Presupuesto')
                    ->money('USD')
                    ->placeholder('No especificado'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => CateringRequest::$statusLabels[$state] ?? ucfirst($state))
                    ->color(fn (string $state): string => match($state) {
                        'pending' => 'warning',
                        'viewed' => 'info',
                        'quoted' => 'primary',
                        'accepted' => 'success',
                        'declined', 'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Recibida')
                    ->dateTime('d M, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(CateringRequest::$statusLabels),
                Tables\Filters\SelectFilter::make('event_type')
                    ->label('Tipo de Evento')
                    ->options(CateringRequest::$eventTypes),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Ver Detalles')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(fn (CateringRequest $record): string => 'Solicitud de ' . $record->contact_name)
                    ->modalContent(fn (CateringRequest $record) => view('filament.owner.modals.catering-request-detail', ['request' => $record]))
                    ->modalSubmitActionLabel('Cerrar')
                    ->modalCancelAction(false)
                    ->action(function (CateringRequest $record): void {
                        if ($record->status === 'pending') {
                            $record->update(['status' => 'viewed']);
                        }
                    }),

                Tables\Actions\Action::make('quote')
                    ->label('Enviar Cotización')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->visible(fn (CateringRequest $record): bool => in_array($record->status, ['pending', 'viewed']))
                    ->form([
                        Forms\Components\TextInput::make('quote_amount')
                            ->label('Monto de Cotización (USD)')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        Forms\Components\Textarea::make('owner_notes')
                            ->label('Mensaje al Cliente')
                            ->placeholder('Incluye detalles del servicio, menu propuesto, terminos...')
                            ->rows(4)
                            ->required(),
                    ])
                    ->action(function (CateringRequest $record, array $data): void {
                        $record->update([
                            'status' => 'quoted',
                            'quote_amount' => $data['quote_amount'],
                            'owner_notes' => $data['owner_notes'],
                            'responded_at' => now(),
                        ]);
                        Notification::make()->title('Cotización guardada')->success()->send();
                    }),

                Tables\Actions\Action::make('decline')
                    ->label('Declinar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (CateringRequest $record): bool => in_array($record->status, ['pending', 'viewed']))
                    ->action(function (CateringRequest $record): void {
                        $record->update(['status' => 'declined', 'responded_at' => now()]);
                        Notification::make()->title('Solicitud declinada')->warning()->send();
                    }),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading(function (): string {
                $restaurant = auth()->user()?->allAccessibleRestaurants()->first();
                return (!$restaurant || !in_array($restaurant->subscription_tier, ['premium', 'elite']))
                    ? '🔒 Función Premium' : 'Sin solicitudes de catering';
            })
            ->emptyStateDescription(function (): string {
                $restaurant = auth()->user()?->allAccessibleRestaurants()->first();
                return (!$restaurant || !in_array($restaurant->subscription_tier, ['premium', 'elite']))
                    ? 'Actualiza tu plan a Premium para recibir solicitudes de catering y eventos privados.'
                    : 'Cuando los clientes soliciten catering, aparecerá aquí.';
            })
            ->emptyStateActions(function (): array {
                $restaurant = auth()->user()?->allAccessibleRestaurants()->first();
                if (!$restaurant || !in_array($restaurant->subscription_tier, ['premium', 'elite'])) {
                    return [
                        Tables\Actions\Action::make('upgrade')
                            ->label('Ver planes Premium')
                            ->url(\App\Filament\Owner\Pages\MySubscription::getUrl())
                            ->color('warning')
                            ->icon('heroicon-o-arrow-up-circle'),
                    ];
                }
                return [];
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCateringRequests::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $restaurantIds = auth()->user()?->allAccessibleRestaurants()?->pluck('id') ?? collect();

        $count = CateringRequest::whereIn('restaurant_id', $restaurantIds)
            ->where('status', 'pending')
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}

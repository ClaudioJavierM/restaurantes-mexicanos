<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FamerSubscriptionResource\Pages;
use App\Filament\Widgets\SubscriptionFunnelWidget;
use App\Models\Restaurant;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FamerSubscriptionResource extends Resource
{
    protected static ?string $model = Restaurant::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Suscripciones';

    protected static ?string $modelLabel = 'Suscripción';

    protected static ?string $pluralModelLabel = 'Suscripciones';

    protected static ?string $navigationGroup = 'Ingresos';

    protected static ?int $navigationSort = 1;

    protected static bool $isLazy = true;

    // ─── Navigation Badge ────────────────────────────────────────────────────

    public static function getNavigationBadge(): ?string
    {
        try {
            return (string) Restaurant::whereNotNull('subscription_tier')
                ->where('subscription_status', 'active')
                ->count();
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'success';
    }

    // ─── Base Query ──────────────────────────────────────────────────────────

    public static function getEloquentQuery(): Builder
    {
        return Restaurant::query()
            ->where(fn (Builder $q) => $q->where('is_claimed', true)
                ->orWhereIn('subscription_tier', ['premium', 'elite']));
    }

    // ─── Form ────────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Plan de Suscripción')
                    ->icon('heroicon-o-credit-card')
                    ->schema([
                        Forms\Components\Select::make('subscription_tier')
                            ->label('Tier')
                            ->options([
                                'claimed' => 'Reclamado',
                                'premium' => 'Premium ⭐ ($29/mes)',
                                'elite'   => 'Elite 👑 ($79/mes)',
                            ])
                            ->nullable()
                            ->placeholder('Sin plan'),

                        Forms\Components\Select::make('subscription_status')
                            ->label('Estado')
                            ->options([
                                'active'   => 'Activo',
                                'canceled' => 'Cancelado',
                                'expired'  => 'Expirado',
                                'past_due' => 'Pago vencido',
                            ])
                            ->nullable()
                            ->placeholder('—'),

                        Forms\Components\DateTimePicker::make('subscription_started_at')
                            ->label('Inicio')
                            ->nullable(),

                        Forms\Components\DateTimePicker::make('subscription_expires_at')
                            ->label('Vencimiento')
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Stripe')
                    ->icon('heroicon-o-lock-closed')
                    ->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('stripe_customer_id')
                            ->label('Customer ID')
                            ->readOnly()
                            ->prefix('cus_'),

                        Forms\Components\TextInput::make('stripe_subscription_id')
                            ->label('Subscription ID')
                            ->readOnly()
                            ->prefix('sub_'),
                    ])->columns(2),
            ]);
    }

    // ─── Table ───────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Restaurante')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->url(fn (Restaurant $record): string => RestaurantResource::getUrl('edit', ['record' => $record])),

                Tables\Columns\TextColumn::make('owner_name')
                    ->label('Propietario')
                    ->description(fn (Restaurant $record): string => $record->owner_email ?? '')
                    ->searchable(['owner_name', 'owner_email'])
                    ->toggleable(),

                Tables\Columns\TextColumn::make('subscription_tier')
                    ->label('Plan')
                    ->badge()
                    ->formatStateUsing(function (?string $state, Restaurant $record): string {
                        if ($record->is_claimed) {
                            return match ($state) {
                                'premium' => 'Premium ⭐',
                                'elite'   => 'Elite 👑',
                                default   => 'Reclamado (Free)',
                            };
                        }
                        return match ($state) {
                            'premium' => 'Premium ⭐',
                            'elite'   => 'Elite 👑',
                            default   => 'Sin reclamar',
                        };
                    })
                    ->color(function (?string $state, Restaurant $record): string {
                        if ($record->is_claimed) {
                            return match ($state) {
                                'premium' => 'success',
                                'elite'   => 'warning',
                                default   => 'info',
                            };
                        }
                        return 'gray';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('subscription_status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'active'   => 'Activo',
                        'canceled' => 'Cancelado',
                        'expired'  => 'Expirado',
                        'past_due' => 'Pago vencido',
                        default    => '—',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'active'   => 'success',
                        'canceled' => 'danger',
                        'expired'  => 'warning',
                        'past_due' => 'danger',
                        default    => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('mrr_contribution')
                    ->label('MRR')
                    ->state(fn (Restaurant $record): string => match ($record->subscription_tier) {
                        'premium' => '$29',
                        'elite'   => '$79',
                        default   => '$0',
                    })
                    ->badge()
                    ->color(fn (Restaurant $record): string => in_array($record->subscription_tier, ['premium', 'elite']) ? 'success' : 'gray')
                    ->sortable(false),

                Tables\Columns\TextColumn::make('subscription_started_at')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('subscription_expires_at')
                    ->label('Vence')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn (Restaurant $record): string => match (true) {
                        $record->subscription_status === 'past_due' => 'danger',
                        $record->subscription_expires_at !== null
                            && Carbon::parse($record->subscription_expires_at)->isFuture()
                            && Carbon::parse($record->subscription_expires_at)->diffInDays(now()) <= 7 => 'warning',
                        default => 'gray',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('profile_views')
                    ->label('Vistas')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subscription_tier')
                    ->label('Plan')
                    ->options([
                        'premium' => 'Premium ⭐',
                        'elite'   => 'Elite 👑',
                    ])
                    ->placeholder('Todos los planes'),

                Tables\Filters\TernaryFilter::make('is_claimed')
                    ->label('Estado de Claim')
                    ->trueLabel('Reclamados')
                    ->falseLabel('Sin reclamar')
                    ->queries(
                        true: fn ($query) => $query->where('is_claimed', true),
                        false: fn ($query) => $query->where('is_claimed', false),
                    ),

                Tables\Filters\SelectFilter::make('subscription_status')
                    ->label('Estado')
                    ->options([
                        'active'   => 'Activo',
                        'canceled' => 'Cancelado',
                        'expired'  => 'Expirado',
                        'past_due' => 'Pago vencido',
                    ])
                    ->placeholder('Todos los estados'),

                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Vence en 30 días')
                    ->query(fn (Builder $query) => $query->whereBetween('subscription_expires_at', [now(), now()->addDays(30)]))
                    ->toggle(),

                Tables\Filters\Filter::make('past_due')
                    ->label('Pago vencido')
                    ->query(fn (Builder $query) => $query->where('subscription_status', 'past_due'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_restaurant')
                    ->label('Ver')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Restaurant $record): string => RestaurantResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('activate')
                    ->label('Marcar Activo')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Restaurant $record): void {
                        $record->update(['subscription_status' => 'active']);
                        Notification::make()
                            ->title('Suscripción activada')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Restaurant $record): bool => $record->subscription_status !== 'active'),

                Tables\Actions\Action::make('cancel')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('¿Cancelar suscripción?')
                    ->modalDescription('Esto marcará la suscripción como cancelada. El restaurante perderá acceso a funciones premium.')
                    ->action(function (Restaurant $record): void {
                        $record->update(['subscription_status' => 'canceled']);
                        Notification::make()
                            ->title('Suscripción cancelada')
                            ->warning()
                            ->send();
                    })
                    ->visible(fn (Restaurant $record): bool => $record->subscription_status === 'active'),

                Tables\Actions\EditAction::make()
                    ->label('Editar plan'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('export')
                        ->label('Exportar seleccionados')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (): void {
                            Notification::make()
                                ->title('Exportación en cola')
                                ->body('La exportación estará lista pronto.')
                                ->info()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('subscription_started_at', 'desc')
            ->emptyStateHeading('Sin suscripciones')
            ->emptyStateDescription('No hay restaurantes con plan activo o reclamado.')
            ->emptyStateIcon('heroicon-o-credit-card');
    }

    // ─── Header Widgets ──────────────────────────────────────────────────────

    public static function getHeaderWidgets(): array
    {
        return [
            SubscriptionFunnelWidget::class,
        ];
    }

    // ─── Relations & Pages ───────────────────────────────────────────────────

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFamerSubscriptions::route('/'),
            'create' => Pages\CreateFamerSubscription::route('/create'),
            'edit'   => Pages\EditFamerSubscription::route('/{record}/edit'),
        ];
    }
}

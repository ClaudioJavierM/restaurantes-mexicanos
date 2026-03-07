<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\MyReservationsResource\Pages;
use App\Models\Reservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyReservationsResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Reservaciones';

    protected static ?string $modelLabel = 'Reservacion';

    protected static ?string $pluralModelLabel = 'Reservaciones';

    protected static ?string $navigationGroup = 'Mi Negocio';

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        // Check team member permissions
        $user2 = auth()->user();
        if ($user2) {
            $teamMember = \App\Models\RestaurantTeamMember::where('user_id', $user2->id)
                ->where('status', 'active')->first();
            if ($teamMember && $teamMember->role !== 'admin') {
                $permissions = $teamMember->permissions ?? [];
                if (!($permissions['reservations'] ?? false)) {
                    return false;
                }
            }
        }

        $restaurant = auth()->user()?->allAccessibleRestaurants()->first();
        return $restaurant && $restaurant->is_claimed;
    }

    public static function canAccess(): bool
    {
        $restaurant = auth()->user()?->allAccessibleRestaurants()->first();
        return $restaurant && $restaurant->is_claimed;
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $restaurantIds = $user->allAccessibleRestaurants()->pluck('id');

        return parent::getEloquentQuery()
            ->whereIn('restaurant_id', $restaurantIds)
            ->latest('reservation_date');
    }

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        if (!$user) return null;

        $restaurantIds = $user->allAccessibleRestaurants()->pluck('id');

        return Reservation::whereIn('restaurant_id', $restaurantIds)
            ->where('status', Reservation::STATUS_PENDING)
            ->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canCreate(): bool
    {
        $restaurant = auth()->user()?->allAccessibleRestaurants()->first();
        return $restaurant && in_array($restaurant->subscription_tier, ["premium", "elite"]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informacion del Cliente')
                    ->schema([
                        Forms\Components\TextInput::make('guest_name')
                            ->label('Nombre')
                            ->disabled(),
                        Forms\Components\TextInput::make('guest_email')
                            ->label('Email')
                            ->disabled(),
                        Forms\Components\TextInput::make('guest_phone')
                            ->label('Telefono')
                            ->disabled(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Detalles de la Reservacion')
                    ->schema([
                        Forms\Components\DatePicker::make('reservation_date')
                            ->label('Fecha')
                            ->disabled(),
                        Forms\Components\TextInput::make('reservation_time')
                            ->label('Hora')
                            ->disabled(),
                        Forms\Components\TextInput::make('party_size')
                            ->label('Personas')
                            ->disabled(),
                        Forms\Components\TextInput::make('occasion')
                            ->label('Ocasion')
                            ->formatStateUsing(fn ($state) => Reservation::getOccasions()[$state] ?? $state)
                            ->disabled(),
                    ])
                    ->columns(4),

                Forms\Components\Section::make('Solicitudes Especiales')
                    ->schema([
                        Forms\Components\Textarea::make('special_requests')
                            ->label('Solicitudes del cliente')
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->collapsed()
                    ->visible(fn ($record) => !empty($record?->special_requests)),

                Forms\Components\Section::make('Notas del Restaurante')
                    ->schema([
                        Forms\Components\Textarea::make('internal_notes')
                            ->label('Notas internas')
                            ->helperText('Solo visible para el equipo del restaurante')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('table_assigned')
                            ->label('Mesa asignada')
                            ->placeholder('Ej: Mesa 5, Terraza'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reservation_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reservation_time')
                    ->label('Hora')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('guest_name')
                    ->label('Cliente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('guest_phone')
                    ->label('Telefono')
                    ->copyable(),
                Tables\Columns\TextColumn::make('party_size')
                    ->label('Personas')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('occasion')
                    ->label('Ocasion')
                    ->formatStateUsing(fn ($state) => Reservation::getOccasions()[$state] ?? '-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        'cancelled' => 'Cancelada',
                        'completed' => 'Completada',
                        'no_show' => 'No asistio',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'danger' => 'cancelled',
                        'info' => 'completed',
                        'gray' => 'no_show',
                    ]),
                Tables\Columns\TextColumn::make('table_assigned')
                    ->label('Mesa')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('confirmation_code')
                    ->label('Codigo')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('reservation_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        'cancelled' => 'Cancelada',
                        'completed' => 'Completada',
                        'no_show' => 'No asistio',
                    ]),
                Tables\Filters\Filter::make('upcoming')
                    ->label('Proximas')
                    ->query(fn (Builder $query) => $query->where('reservation_date', '>=', now()->toDateString())),
                Tables\Filters\Filter::make('today')
                    ->label('Hoy')
                    ->query(fn (Builder $query) => $query->whereDate('reservation_date', now())),
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->label('Confirmar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === Reservation::STATUS_PENDING)
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar Reservacion')
                    ->modalDescription('Se enviara una notificacion al cliente confirmando su reservacion.')
                    ->action(fn ($record) => $record->confirm()),
                Tables\Actions\Action::make('cancel')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => in_array($record->status, [Reservation::STATUS_PENDING, Reservation::STATUS_CONFIRMED]))
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Motivo de cancelacion')
                            ->required(),
                    ])
                    ->action(fn ($record, array $data) => $record->cancel($data['reason'])),
                Tables\Actions\Action::make('complete')
                    ->label('Marcar completada')
                    ->icon('heroicon-o-check')
                    ->color('info')
                    ->visible(fn ($record) => $record->status === Reservation::STATUS_CONFIRMED)
                    ->action(fn ($record) => $record->complete()),
                Tables\Actions\Action::make('no_show')
                    ->label('No asistio')
                    ->icon('heroicon-o-user-minus')
                    ->color('gray')
                    ->visible(fn ($record) => $record->status === Reservation::STATUS_CONFIRMED)
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->markNoShow()),
                Tables\Actions\Action::make('call')
                    ->label('Llamar')
                    ->icon('heroicon-o-phone')
                    ->url(fn ($record) => 'tel:' . $record->guest_phone)
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make()
                    ->label('Notas'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('confirm_all')
                        ->label('Confirmar seleccionadas')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->confirm())
                        ->requiresConfirmation(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyReservations::route('/'),
            'edit' => Pages\EditMyReservation::route('/{record}/edit'),
        ];
    }
}

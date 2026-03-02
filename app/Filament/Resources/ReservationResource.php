<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Models\Reservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Reservaciones';

    protected static ?string $modelLabel = 'Reservacion';

    protected static ?string $pluralModelLabel = 'Reservaciones';

    protected static ?string $navigationGroup = 'Contenido';

    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')
            ->where('reservation_date', '>=', now()->toDateString())
            ->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $pending = static::getModel()::where('status', 'pending')
            ->where('reservation_date', '>=', now()->toDateString())
            ->count();
        return $pending > 0 ? 'warning' : 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalles de la Reservacion')
                    ->schema([
                        Forms\Components\Select::make('restaurant_id')
                            ->relationship('restaurant', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('reservation_date')
                            ->label('Fecha')
                            ->required(),
                        Forms\Components\TimePicker::make('reservation_time')
                            ->label('Hora')
                            ->required()
                            ->seconds(false),
                        Forms\Components\Select::make('party_size')
                            ->label('Numero de Personas')
                            ->options(array_combine(range(1, 20), range(1, 20)))
                            ->required(),
                        Forms\Components\Select::make('occasion')
                            ->label('Ocasion')
                            ->options(Reservation::getOccasions()),
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'pending' => 'Pendiente',
                                'confirmed' => 'Confirmada',
                                'cancelled' => 'Cancelada',
                                'completed' => 'Completada',
                                'no_show' => 'No se presento',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Informacion de Contacto')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Usuario Registrado')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('guest_name')
                            ->label('Nombre del Invitado')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('guest_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('guest_phone')
                            ->label('Telefono')
                            ->tel()
                            ->maxLength(20),
                    ])->columns(2),

                Forms\Components\Section::make('Notas')
                    ->schema([
                        Forms\Components\Textarea::make('special_requests')
                            ->label('Peticiones Especiales')
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('internal_notes')
                            ->label('Notas Internas')
                            ->helperText('Solo visible para el personal')
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('table_assigned')
                            ->label('Mesa Asignada')
                            ->maxLength(50),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('confirmation_code')
                    ->label('Codigo')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('restaurant.name')
                    ->label('Restaurante')
                    ->searchable()
                    ->sortable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('contact_name')
                    ->label('Cliente')
                    ->getStateUsing(fn (Reservation $record) => $record->getContactName())
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function ($q) use ($search) {
                            $q->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                              ->orWhere('guest_name', 'like', "%{$search}%");
                        });
                    }),
                Tables\Columns\TextColumn::make('reservation_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reservation_time')
                    ->label('Hora')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('party_size')
                    ->label('Personas')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (Reservation $record) => $record->getStatusLabel())
                    ->color(fn (Reservation $record) => $record->getStatusColor()),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('reservation_date', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        'cancelled' => 'Cancelada',
                        'completed' => 'Completada',
                        'no_show' => 'No se presento',
                    ]),
                Tables\Filters\Filter::make('upcoming')
                    ->label('Proximas')
                    ->query(fn (Builder $query): Builder => $query->where('reservation_date', '>=', now()->toDateString()))
                    ->default(),
                Tables\Filters\Filter::make('today')
                    ->label('Hoy')
                    ->query(fn (Builder $query): Builder => $query->whereDate('reservation_date', now())),
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->label('Confirmar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Reservation $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Reservation $record) {
                        $record->confirm();
                        Notification::make()
                            ->success()
                            ->title('Reservacion Confirmada')
                            ->send();
                    }),
                Tables\Actions\Action::make('cancel')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Reservation $record) => in_array($record->status, ['pending', 'confirmed']))
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Razon de cancelacion')
                            ->maxLength(500),
                    ])
                    ->action(function (Reservation $record, array $data) {
                        $record->cancel($data['reason'] ?? null);
                        Notification::make()
                            ->success()
                            ->title('Reservacion Cancelada')
                            ->send();
                    }),
                Tables\Actions\Action::make('complete')
                    ->label('Completar')
                    ->icon('heroicon-o-check-badge')
                    ->color('info')
                    ->visible(fn (Reservation $record) => $record->status === 'confirmed')
                    ->requiresConfirmation()
                    ->action(function (Reservation $record) {
                        $record->complete();
                        Notification::make()
                            ->success()
                            ->title('Reservacion Completada')
                            ->send();
                    }),
                Tables\Actions\Action::make('no_show')
                    ->label('No se presento')
                    ->icon('heroicon-o-user-minus')
                    ->color('gray')
                    ->visible(fn (Reservation $record) => $record->status === 'confirmed')
                    ->requiresConfirmation()
                    ->action(function (Reservation $record) {
                        $record->markNoShow();
                        Notification::make()
                            ->warning()
                            ->title('Marcado como No Show')
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('confirm_selected')
                        ->label('Confirmar Seleccionadas')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(fn (Reservation $record) => $record->confirm());
                            Notification::make()
                                ->success()
                                ->title('Reservaciones Confirmadas')
                                ->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}

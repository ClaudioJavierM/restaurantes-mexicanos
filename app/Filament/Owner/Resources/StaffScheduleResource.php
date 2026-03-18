<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\StaffScheduleResource\Pages;
use App\Models\StaffMember;
use App\Models\StaffShift;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StaffScheduleResource extends Resource
{
    protected static ?string $model = StaffMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Personal y Turnos';
    protected static ?string $modelLabel = 'Empleado';
    protected static ?string $pluralModelLabel = 'Personal';
    protected static ?string $navigationGroup = 'Mi Negocio';
    protected static ?int $navigationSort = 11;

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

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nombre completo')
                ->required(),

            Forms\Components\Select::make('role')
                ->label('Puesto')
                ->options(StaffMember::$roles)
                ->required(),

            Forms\Components\TextInput::make('phone')
                ->label('Teléfono')
                ->tel(),

            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->email(),

            Forms\Components\TextInput::make('hourly_rate')
                ->label('Tarifa por hora (USD)')
                ->numeric()
                ->prefix('$'),

            Forms\Components\Textarea::make('notes')
                ->label('Notas')
                ->rows(2),

            Forms\Components\Toggle::make('is_active')
                ->label('Activo')
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('role')
                    ->label('Puesto')
                    ->formatStateUsing(fn (string $state): string => StaffMember::$roles[$state] ?? ucfirst($state))
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('hourly_rate')
                    ->label('$/hr')
                    ->money('USD')
                    ->placeholder('—'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),

                Tables\Columns\TextColumn::make('shifts_count')
                    ->label('Turnos esta semana')
                    ->counts([
                        'shifts' => fn (Builder $q) => $q
                            ->where('shift_date', '>=', now()->startOfWeek())
                            ->where('shift_date', '<=', now()->endOfWeek()),
                    ])
                    ->default(0),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Agregar Empleado')
                    ->mutateFormDataUsing(function (array $data): array {
                        $restaurant = auth()->user()->allAccessibleRestaurants()->first();
                        $data['restaurant_id'] = $restaurant->id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('add_shift')
                    ->label('Agregar Turno')
                    ->icon('heroicon-o-calendar-days')
                    ->color('success')
                    ->form([
                        Forms\Components\DatePicker::make('shift_date')
                            ->label('Fecha')
                            ->required()
                            ->default(now()),
                        Forms\Components\TimePicker::make('start_time')
                            ->label('Hora inicio')
                            ->required()
                            ->default('09:00'),
                        Forms\Components\TimePicker::make('end_time')
                            ->label('Hora fin')
                            ->required()
                            ->default('17:00'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas del turno')
                            ->rows(1),
                    ])
                    ->action(function (StaffMember $record, array $data): void {
                        $restaurant = auth()->user()->allAccessibleRestaurants()->first();
                        StaffShift::create([
                            'restaurant_id'   => $restaurant->id,
                            'staff_member_id' => $record->id,
                            'shift_date'      => $data['shift_date'],
                            'start_time'      => $data['start_time'],
                            'end_time'        => $data['end_time'],
                            'notes'           => $data['notes'] ?? null,
                            'status'          => 'scheduled',
                        ]);
                        Notification::make()->title('Turno agregado')->success()->send();
                    }),

                Tables\Actions\Action::make('view_shifts')
                    ->label('Ver Turnos')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (StaffMember $record): string =>
                        static::getUrl('shifts', ['record' => $record])),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStaffMembers::route('/'),
            'create' => Pages\CreateStaffMember::route('/create'),
            'edit'   => Pages\EditStaffMember::route('/{record}/edit'),
            'shifts' => Pages\ManageStaffShifts::route('/{record}/turnos'),
        ];
    }
}

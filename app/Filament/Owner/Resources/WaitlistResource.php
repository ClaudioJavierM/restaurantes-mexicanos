<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\WaitlistResource\Pages;
use App\Models\RestaurantWaitlist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WaitlistResource extends Resource
{
    protected static ?string $model = RestaurantWaitlist::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationLabel = 'Lista de Espera';
    protected static ?string $modelLabel = 'Entrada';
    protected static ?string $pluralModelLabel = 'Lista de Espera';
    protected static ?string $navigationGroup = 'Mi Negocio';
    protected static ?int $navigationSort = 6;

    public static function getEloquentQuery(): Builder
    {
        $restaurantIds = auth()->user()->allAccessibleRestaurants()->pluck('id');

        return parent::getEloquentQuery()
            ->whereIn('restaurant_id', $restaurantIds)
            ->where('created_at', '>=', now()->startOfDay())
            ->orderBy('created_at');
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
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('position')
                    ->label('#')
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Cliente')
                    ->searchable(),

                Tables\Columns\TextColumn::make('party_size')
                    ->label('Personas')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => '👥 ' . $state),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('special_request')
                    ->label('Nota')
                    ->limit(30)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('wait_time')
                    ->label('Espera')
                    ->getStateUsing(fn (RestaurantWaitlist $record): string =>
                        $record->created_at->diffForHumans(null, true)
                    ),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => RestaurantWaitlist::$statusLabels[$state] ?? ucfirst($state))
                    ->color(fn (string $state): string => match($state) {
                        'waiting'   => 'warning',
                        'called'    => 'info',
                        'seated'    => 'success',
                        'no_show', 'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(RestaurantWaitlist::$statusLabels)
                    ->default('waiting'),
            ])
            ->actions([
                Tables\Actions\Action::make('call')
                    ->label('Llamar')
                    ->icon('heroicon-o-megaphone')
                    ->color('info')
                    ->visible(fn (RestaurantWaitlist $record): bool => $record->status === 'waiting')
                    ->action(function (RestaurantWaitlist $record): void {
                        $record->update(['status' => 'called', 'called_at' => now()]);
                        Notification::make()
                            ->title('Mesa lista para ' . $record->name)
                            ->success()->send();
                    }),

                Tables\Actions\Action::make('seat')
                    ->label('Sentar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (RestaurantWaitlist $record): bool => $record->status === 'called')
                    ->action(function (RestaurantWaitlist $record): void {
                        $record->update(['status' => 'seated', 'seated_at' => now()]);
                        RestaurantWaitlist::recalculatePositions($record->restaurant_id);
                        Notification::make()->title($record->name . ' ha sido sentado')->success()->send();
                    }),

                Tables\Actions\Action::make('no_show')
                    ->label('No llegó')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (RestaurantWaitlist $record): bool => $record->status === 'called')
                    ->requiresConfirmation()
                    ->action(function (RestaurantWaitlist $record): void {
                        $record->update(['status' => 'no_show']);
                        RestaurantWaitlist::recalculatePositions($record->restaurant_id);
                    }),

                Tables\Actions\Action::make('cancel')
                    ->label('Cancelar')
                    ->icon('heroicon-o-trash')
                    ->color('gray')
                    ->visible(fn (RestaurantWaitlist $record): bool => $record->status === 'waiting')
                    ->requiresConfirmation()
                    ->action(function (RestaurantWaitlist $record): void {
                        $record->update(['status' => 'cancelled']);
                        RestaurantWaitlist::recalculatePositions($record->restaurant_id);
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('add_to_waitlist')
                    ->label('Agregar a la Lista')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del cliente')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('party_size')
                                ->label('Número de personas')
                                ->numeric()
                                ->default(2)
                                ->minValue(1)
                                ->maxValue(50)
                                ->required(),
                            Forms\Components\TextInput::make('phone')
                                ->label('Teléfono')
                                ->tel()
                                ->maxLength(20),
                        ]),
                        Forms\Components\TextInput::make('special_request')
                            ->label('Nota especial')
                            ->placeholder('Silla alta, cumpleaños, alergias...')
                            ->maxLength(200),
                    ])
                    ->action(function (array $data): void {
                        $restaurant = auth()->user()->allAccessibleRestaurants()->first();

                        $position = RestaurantWaitlist::where('restaurant_id', $restaurant->id)
                            ->where('status', 'waiting')->count() + 1;

                        RestaurantWaitlist::create([
                            'restaurant_id' => $restaurant->id,
                            'name' => $data['name'],
                            'party_size' => $data['party_size'],
                            'phone' => $data['phone'] ?? null,
                            'special_request' => $data['special_request'] ?? null,
                            'status' => 'waiting',
                            'position' => $position,
                        ]);

                        Notification::make()->title($data['name'] . ' agregado a la lista')->success()->send();
                    }),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'asc')
            ->poll('30s'); // auto-refresh every 30s
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWaitlist::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $restaurantIds = auth()->user()?->allAccessibleRestaurants()?->pluck('id') ?? collect();

        $count = RestaurantWaitlist::whereIn('restaurant_id', $restaurantIds)
            ->where('status', 'waiting')
            ->where('created_at', '>=', now()->startOfDay())
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}

<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\EventsResource\Pages;
use App\Models\RestaurantEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventsResource extends Resource
{
    protected static ?string $model = RestaurantEvent::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Eventos';
    protected static ?string $modelLabel = 'Evento';
    protected static ?string $pluralModelLabel = 'Eventos';
    protected static ?string $navigationGroup = 'Mi Negocio';
    protected static ?int $navigationSort = 8;

    public static function getEloquentQuery(): Builder
    {
        $restaurantIds = auth()->user()->allAccessibleRestaurants()->pluck('id');
        return parent::getEloquentQuery()->whereIn('restaurant_id', $restaurantIds);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informacion del Evento')
                ->schema([
                    Forms\Components\Hidden::make('restaurant_id')
                        ->default(fn () => auth()->user()->allAccessibleRestaurants()->first()?->id),
                    Forms\Components\TextInput::make('title')->label('Titulo')->required()->maxLength(255),
                    Forms\Components\TextInput::make('title_en')->label('Titulo (Ingles)')->maxLength(255),
                    Forms\Components\Select::make('event_type')->label('Tipo de Evento')
                        ->options(RestaurantEvent::EVENT_TYPES)->required()->default('live_music'),
                    Forms\Components\Textarea::make('description')->label('Descripcion')->rows(3),
                ])->columns(2),
            Forms\Components\Section::make('Fecha y Hora')
                ->schema([
                    Forms\Components\DatePicker::make('event_date')->label('Fecha')->required()->default(now()->addWeek()),
                    Forms\Components\TimePicker::make('start_time')->label('Hora Inicio')->required()->default('19:00'),
                    Forms\Components\TimePicker::make('end_time')->label('Hora Fin'),
                ])->columns(3),
            Forms\Components\Section::make('Capacidad y Precio')
                ->schema([
                    Forms\Components\TextInput::make('price')->label('Precio')->numeric()->prefix('$'),
                    Forms\Components\TextInput::make('capacity')->label('Capacidad')->numeric(),
                    Forms\Components\Toggle::make('is_featured')->label('Destacado'),
                    Forms\Components\Toggle::make('is_active')->label('Activo')->default(true),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Evento')->searchable()->limit(30),
                Tables\Columns\TextColumn::make('event_type')->label('Tipo')->badge(),
                Tables\Columns\TextColumn::make('event_date')->label('Fecha')->date('d M, Y')->sortable(),
                Tables\Columns\TextColumn::make('start_time')->label('Hora')->time('h:i A'),
                Tables\Columns\TextColumn::make('price')->label('Precio')->money('usd')->default('Gratis'),
                Tables\Columns\IconColumn::make('is_active')->label('Activo')->boolean(),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->defaultSort('event_date', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Check team member permissions
        $user2 = auth()->user();
        if ($user2) {
            $teamMember = \App\Models\RestaurantTeamMember::where('user_id', $user2->id)
                ->where('status', 'active')->first();
            if ($teamMember && $teamMember->role !== 'admin') {
                $permissions = $teamMember->permissions ?? [];
                if (!($permissions['events'] ?? false)) {
                    return false;
                }
            }
        }

        $user = auth()->user();
        if (!$user) return false;
        $restaurant = $user->allAccessibleRestaurants()->first();
        return $restaurant && $restaurant->is_claimed;
    }

    public static function canCreate(): bool
    {
        $restaurant = auth()->user()?->allAccessibleRestaurants()->first();
        return $restaurant && in_array($restaurant->subscription_tier, ['premium', 'elite']);
    }

    public static function getNavigationBadge(): ?string
    {
        $restaurant = auth()->user()?->allAccessibleRestaurants()->first();
        if ($restaurant && !in_array($restaurant->subscription_tier, ['premium', 'elite'])) {
            return 'PRO';
        }
        return null;
    }

    public static function getNavigationBadgeColor(): ?string { return 'warning'; }
}

<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\MyCustomersResource\Pages;
use App\Models\RestaurantCustomer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyCustomersResource extends Resource
{
    protected static ?string $model = RestaurantCustomer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Clientes';

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $pluralModelLabel = 'Clientes';

    protected static ?string $navigationGroup = 'Mi Negocio';

    protected static ?int $navigationSort = 4;

    /**
     * Only show navigation item if the user has at least one claimed restaurant.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->restaurants()->where('is_claimed', true)->exists() ?? false;
    }

    /**
     * Check if the current user has access (premium or elite tier).
     */
    protected static function userHasPremiumAccess(): bool
    {
        return auth()->user()
            ?->restaurants()
            ->where('is_claimed', true)
            ->whereIn('subscription_tier', ['premium', 'elite'])
            ->exists() ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        $restaurantIds = auth()->user()->restaurants()->pluck('id');

        return parent::getEloquentQuery()
            ->whereIn('restaurant_id', $restaurantIds)
            ->latest('last_visit_at');
    }

    public static function form(Form $form): Form
    {
        // Read-only — customers are system-created
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información del Cliente')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('display_name')
                            ->label('Nombre')
                            ->getStateUsing(fn (RestaurantCustomer $record) => $record->display_name),

                        Infolists\Components\TextEntry::make('email')
                            ->label('Email')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('phone')
                            ->label('Teléfono')
                            ->placeholder('—'),

                        Infolists\Components\TextEntry::make('birthday')
                            ->label('Cumpleaños')
                            ->date('d M, Y')
                            ->placeholder('—'),

                        Infolists\Components\TextEntry::make('source')
                            ->label('Fuente')
                            ->badge()
                            ->color(fn (?string $state) => match ($state) {
                                'reservation'  => 'info',
                                'order'        => 'success',
                                'manual'       => 'gray',
                                'import'       => 'warning',
                                default        => 'gray',
                            }),

                        Infolists\Components\TextEntry::make('tags')
                            ->label('Etiquetas')
                            ->badge()
                            ->getStateUsing(fn (RestaurantCustomer $record) => $record->tags ?? [])
                            ->separator(','),
                    ]),

                Infolists\Components\Section::make('Actividad & Lealtad')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('visits_count')
                            ->label('Visitas totales')
                            ->suffix(' visitas'),

                        Infolists\Components\TextEntry::make('total_spent')
                            ->label('Total gastado')
                            ->money('MXN'),

                        Infolists\Components\TextEntry::make('points')
                            ->label('Puntos acumulados')
                            ->suffix(' pts'),

                        Infolists\Components\TextEntry::make('last_visit_at')
                            ->label('Última visita')
                            ->dateTime('d M, Y H:i')
                            ->placeholder('Sin visitas'),
                    ]),

                Infolists\Components\Section::make('Suscripciones')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\IconEntry::make('email_subscribed')
                            ->label('Email')
                            ->boolean()
                            ->trueIcon('heroicon-o-envelope')
                            ->falseIcon('heroicon-o-envelope-open')
                            ->trueColor('success')
                            ->falseColor('gray'),

                        Infolists\Components\IconEntry::make('sms_subscribed')
                            ->label('SMS')
                            ->boolean()
                            ->trueIcon('heroicon-o-device-phone-mobile')
                            ->falseIcon('heroicon-o-device-phone-mobile')
                            ->trueColor('success')
                            ->falseColor('gray'),

                        Infolists\Components\TextEntry::make('subscribed_at')
                            ->label('Suscrito desde')
                            ->dateTime('d M, Y')
                            ->placeholder('—'),

                        Infolists\Components\TextEntry::make('unsubscribed_at')
                            ->label('Desuscrito el')
                            ->dateTime('d M, Y')
                            ->placeholder('—'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        // If the user does not have a premium/elite restaurant, show an upgrade notice
        if (! static::userHasPremiumAccess()) {
            return $table
                ->columns([
                    Tables\Columns\TextColumn::make('email')
                        ->label('Email'),
                ])
                ->emptyStateIcon('heroicon-o-lock-closed')
                ->emptyStateHeading('Función PRO')
                ->emptyStateDescription('Actualiza tu plan a Premium o Elite para acceder a la base de datos de clientes, exportar contactos y ver métricas de lealtad.')
                ->emptyStateActions([
                    Tables\Actions\Action::make('upgrade')
                        ->label('Ver planes')
                        ->icon('heroicon-o-arrow-up-circle')
                        ->color('warning')
                        ->url('/owner/my-restaurant')
                        ->openUrlInNewTab(),
                ])
                ->paginated(false)
                ->modifyQueryUsing(fn (Builder $query) => $query->whereRaw('1 = 0'));
        }

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('display_name')
                    ->label('Cliente')
                    ->getStateUsing(fn (RestaurantCustomer $record) => $record->display_name)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                        });
                    })
                    ->sortable('name')
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->placeholder('—')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('visits_count')
                    ->label('Visitas')
                    ->badge()
                    ->color(fn (int $state): string => $state > 5 ? 'warning' : 'gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_spent')
                    ->label('Total gastado')
                    ->money('MXN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('points')
                    ->label('Puntos')
                    ->formatStateUsing(fn (int $state) => "⭐ {$state}")
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_visit_at')
                    ->label('Última visita')
                    ->since()
                    ->sortable()
                    ->placeholder('—')
                    ->color('gray'),

                Tables\Columns\IconColumn::make('email_subscribed')
                    ->label('Email')
                    ->boolean()
                    ->trueIcon('heroicon-o-envelope')
                    ->falseIcon('heroicon-o-envelope-open')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\IconColumn::make('sms_subscribed')
                    ->label('SMS')
                    ->boolean()
                    ->trueIcon('heroicon-o-device-phone-mobile')
                    ->falseIcon('heroicon-o-device-phone-mobile')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('tags')
                    ->label('Etiquetas')
                    ->badge()
                    ->getStateUsing(fn (RestaurantCustomer $record) => collect($record->tags ?? [])->take(3)->toArray())
                    ->separator(',')
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('email_subscribed')
                    ->label('Suscrito por email')
                    ->placeholder('Todos')
                    ->trueLabel('Sí')
                    ->falseLabel('No'),

                Tables\Filters\TernaryFilter::make('sms_subscribed')
                    ->label('Suscrito por SMS')
                    ->placeholder('Todos')
                    ->trueLabel('Sí')
                    ->falseLabel('No'),

                Tables\Filters\Filter::make('inactive')
                    ->label('Inactivos +90 días')
                    ->query(fn (Builder $query) => $query->inactive(90))
                    ->toggle(),

                Tables\Filters\Filter::make('birthday_this_month')
                    ->label('Cumpleaños este mes')
                    ->query(fn (Builder $query) => $query->birthdayThisMonth())
                    ->toggle(),

                Tables\Filters\SelectFilter::make('source')
                    ->label('Fuente')
                    ->options([
                        'reservation' => 'Reservación',
                        'order'       => 'Orden',
                        'manual'      => 'Manual',
                        'import'      => 'Importación',
                    ])
                    ->placeholder('Todas las fuentes'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->infolist([
                        Infolists\Components\Section::make('Información del Cliente')
                            ->columns(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('display_name')
                                    ->label('Nombre')
                                    ->getStateUsing(fn (RestaurantCustomer $record) => $record->display_name),

                                Infolists\Components\TextEntry::make('email')
                                    ->label('Email')
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('phone')
                                    ->label('Teléfono')
                                    ->placeholder('—'),

                                Infolists\Components\TextEntry::make('birthday')
                                    ->label('Cumpleaños')
                                    ->date('d M, Y')
                                    ->placeholder('—'),

                                Infolists\Components\TextEntry::make('source')
                                    ->label('Fuente')
                                    ->badge()
                                    ->color(fn (?string $state) => match ($state) {
                                        'reservation' => 'info',
                                        'order'       => 'success',
                                        'manual'      => 'gray',
                                        'import'      => 'warning',
                                        default       => 'gray',
                                    }),

                                Infolists\Components\TextEntry::make('tags')
                                    ->label('Etiquetas')
                                    ->badge()
                                    ->getStateUsing(fn (RestaurantCustomer $record) => $record->tags ?? [])
                                    ->separator(','),
                            ]),

                        Infolists\Components\Section::make('Actividad & Lealtad')
                            ->columns(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('visits_count')
                                    ->label('Visitas totales')
                                    ->suffix(' visitas'),

                                Infolists\Components\TextEntry::make('total_spent')
                                    ->label('Total gastado')
                                    ->money('MXN'),

                                Infolists\Components\TextEntry::make('points')
                                    ->label('Puntos acumulados')
                                    ->suffix(' pts'),

                                Infolists\Components\TextEntry::make('last_visit_at')
                                    ->label('Última visita')
                                    ->dateTime('d M, Y H:i')
                                    ->placeholder('Sin visitas'),
                            ]),

                        Infolists\Components\Section::make('Suscripciones')
                            ->columns(2)
                            ->schema([
                                Infolists\Components\IconEntry::make('email_subscribed')
                                    ->label('Email')
                                    ->boolean()
                                    ->trueColor('success')
                                    ->falseColor('gray'),

                                Infolists\Components\IconEntry::make('sms_subscribed')
                                    ->label('SMS')
                                    ->boolean()
                                    ->trueColor('success')
                                    ->falseColor('gray'),

                                Infolists\Components\TextEntry::make('subscribed_at')
                                    ->label('Suscrito desde')
                                    ->dateTime('d M, Y')
                                    ->placeholder('—'),

                                Infolists\Components\TextEntry::make('unsubscribed_at')
                                    ->label('Desuscrito el')
                                    ->dateTime('d M, Y')
                                    ->placeholder('—'),
                            ]),
                    ])
                    ->modalHeading('Detalle del Cliente')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),

                Tables\Actions\Action::make('export_csv')
                    ->label('Exportar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (RestaurantCustomer $record) {
                        $restaurantIds = auth()->user()->restaurants()->pluck('id');
                        $customers = RestaurantCustomer::whereIn('restaurant_id', $restaurantIds)->get();

                        $csv = static::buildCsv($customers);

                        return response()->streamDownload(function () use ($csv) {
                            echo $csv;
                        }, 'clientes-' . now()->format('Y-m-d') . '.csv', [
                            'Content-Type' => 'text/csv; charset=UTF-8',
                        ]);
                    })
                    ->tooltip('Exportar todos los clientes a CSV'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_all_csv')
                    ->label('Exportar CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        $restaurantIds = auth()->user()->restaurants()->pluck('id');
                        $customers = RestaurantCustomer::whereIn('restaurant_id', $restaurantIds)->get();

                        $csv = static::buildCsv($customers);

                        return response()->streamDownload(function () use ($csv) {
                            echo $csv;
                        }, 'clientes-' . now()->format('Y-m-d') . '.csv', [
                            'Content-Type' => 'text/csv; charset=UTF-8',
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('export_selected_csv')
                    ->label('Exportar seleccionados')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        $csv = static::buildCsv($records);

                        return response()->streamDownload(function () use ($csv) {
                            echo $csv;
                        }, 'clientes-seleccionados-' . now()->format('Y-m-d') . '.csv', [
                            'Content-Type' => 'text/csv; charset=UTF-8',
                        ]);
                    }),
            ])
            ->defaultSort('last_visit_at', 'desc')
            ->poll('120s');
    }

    /**
     * Build a UTF-8 CSV string from a collection of RestaurantCustomer records.
     */
    protected static function buildCsv(\Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $customers): string
    {
        $headers = [
            'Nombre',
            'Email',
            'Teléfono',
            'Cumpleaños',
            'Fuente',
            'Visitas',
            'Total Gastado',
            'Puntos',
            'Última Visita',
            'Email Suscrito',
            'SMS Suscrito',
            'Suscrito Desde',
            'Desuscrito El',
            'Etiquetas',
        ];

        $rows = $customers->map(function (RestaurantCustomer $c) {
            return [
                $c->display_name,
                $c->email,
                $c->phone ?? '',
                $c->birthday?->format('Y-m-d') ?? '',
                $c->source ?? '',
                $c->visits_count,
                number_format((float) $c->total_spent, 2),
                $c->points,
                $c->last_visit_at?->format('Y-m-d H:i') ?? '',
                $c->email_subscribed ? 'Sí' : 'No',
                $c->sms_subscribed ? 'Sí' : 'No',
                $c->subscribed_at?->format('Y-m-d') ?? '',
                $c->unsubscribed_at?->format('Y-m-d') ?? '',
                implode(', ', $c->tags ?? []),
            ];
        });

        // BOM for Excel UTF-8 compatibility
        $output = "\xEF\xBB\xBF";
        $output .= implode(',', array_map(fn ($h) => '"' . str_replace('"', '""', $h) . '"', $headers)) . "\n";

        foreach ($rows as $row) {
            $output .= implode(',', array_map(fn ($v) => '"' . str_replace('"', '""', (string) $v) . '"', $row)) . "\n";
        }

        return $output;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyCustomers::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        if (! static::userHasPremiumAccess()) {
            return 'PRO';
        }

        return null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }
}

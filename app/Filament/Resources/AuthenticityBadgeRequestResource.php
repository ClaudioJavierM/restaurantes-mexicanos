<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuthenticityBadgeRequestResource\Pages;
use App\Models\AuthenticityBadgeRequest;
use App\Models\Restaurant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class AuthenticityBadgeRequestResource extends Resource
{
    protected static ?string $model = AuthenticityBadgeRequest::class;

    protected static ?string $navigationIcon  = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Insignias Autenticidad';
    protected static ?string $navigationGroup = 'Restaurantes';
    protected static ?int    $navigationSort  = 2;

    // ── Form ────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Solicitud')
                    ->schema([
                        Forms\Components\Select::make('restaurant_id')
                            ->label('Restaurante')
                            ->relationship('restaurant', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('badge_id')
                            ->label('Insignia')
                            ->options(
                                collect(AuthenticityBadgeRequest::$catalog)
                                    ->mapWithKeys(fn ($b) => [$b['id'] => $b['icon'] . ' ' . $b['name']])
                            )
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'pending'  => 'Pendiente',
                                'approved' => 'Aprobado',
                                'rejected' => 'Rechazado',
                            ])
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Evidencia del Dueño')
                    ->schema([
                        Forms\Components\Textarea::make('evidence')
                            ->label('Evidencia proporcionada')
                            ->rows(4)
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Notas del Admin')
                    ->schema([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Notas internas')
                            ->rows(3)
                            ->helperText('Visibles solo para el equipo de FAMER, no para el dueño.'),

                        Forms\Components\DateTimePicker::make('reviewed_at')
                            ->label('Revisado el'),
                    ])->columns(2),
            ]);
    }

    // ── Table ───────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        $badgeOptions = collect(AuthenticityBadgeRequest::$catalog)
            ->mapWithKeys(fn ($b) => [$b['id'] => $b['icon'] . ' ' . $b['name']])
            ->toArray();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('restaurant.name')
                    ->label('Restaurante')
                    ->searchable()
                    ->sortable()
                    ->limit(35),

                Tables\Columns\TextColumn::make('badge_id')
                    ->label('Insignia')
                    ->formatStateUsing(function (string $state): string {
                        $badge = AuthenticityBadgeRequest::$catalog[$state] ?? null;
                        return $badge ? $badge['icon'] . ' ' . $badge['name'] : $state;
                    })
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'  => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending'  => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                        default    => $state,
                    }),

                Tables\Columns\TextColumn::make('evidence')
                    ->label('Evidencia')
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->evidence)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Solicitado')
                    ->since()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('Revisado')
                    ->since()
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending'  => 'Pendiente',
                        'approved' => 'Aprobado',
                        'rejected' => 'Rechazado',
                    ]),

                Tables\Filters\SelectFilter::make('badge_id')
                    ->label('Insignia')
                    ->options($badgeOptions),
            ])
            ->actions([
                // ── Approve ──────────────────────────────────────────
                Tables\Actions\Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Aprobar insignia')
                    ->modalDescription(fn (AuthenticityBadgeRequest $record): string =>
                        'Se otorgará la insignia "' .
                        (AuthenticityBadgeRequest::$catalog[$record->badge_id]['name'] ?? $record->badge_id) .
                        '" al restaurante ' . ($record->restaurant?->name ?? '') . '.'
                    )
                    ->visible(fn (AuthenticityBadgeRequest $record): bool => $record->status !== 'approved')
                    ->action(function (AuthenticityBadgeRequest $record): void {
                        $restaurant = $record->restaurant;
                        if (!$restaurant) return;

                        $badgeDef = AuthenticityBadgeRequest::$catalog[$record->badge_id] ?? null;
                        if (!$badgeDef) return;

                        // Build the badge entry for the JSON column
                        $badgeEntry = [
                            'id'          => $badgeDef['id'],
                            'icon'        => $badgeDef['icon'],
                            'name'        => $badgeDef['name'],
                            'name_en'     => $badgeDef['name_en'],
                            'color'       => $badgeDef['color'],
                            'verified_at' => Carbon::now()->toDateString(),
                            'verified_by' => 'admin',
                        ];

                        // Merge into the restaurant's existing badges (no duplicates)
                        $current = $restaurant->authenticity_badges;

                        // Remove old entry for same badge_id if exists, then push new
                        $filtered = array_values(
                            array_filter($current, fn ($b) => $b['id'] !== $badgeDef['id'])
                        );
                        $filtered[] = $badgeEntry;

                        $restaurant->update(['authenticity_badges' => json_encode($filtered)]);

                        // Update request record
                        $record->update([
                            'status'      => 'approved',
                            'reviewed_at' => now(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Insignia aprobada')
                            ->body('La insignia fue agregada al perfil del restaurante.')
                            ->send();
                    }),

                // ── Reject ───────────────────────────────────────────
                Tables\Actions\Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Motivo del rechazo (opcional)')
                            ->rows(3)
                            ->helperText('Esta nota es interna — no se muestra al dueño.'),
                    ])
                    ->visible(fn (AuthenticityBadgeRequest $record): bool => $record->status !== 'rejected')
                    ->action(function (AuthenticityBadgeRequest $record, array $data): void {
                        $record->update([
                            'status'      => 'rejected',
                            'admin_notes' => $data['admin_notes'] ?? null,
                            'reviewed_at' => now(),
                        ]);

                        Notification::make()
                            ->warning()
                            ->title('Solicitud rechazada')
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // ── Pages ───────────────────────────────────────────────────────

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuthenticityBadgeRequests::route('/'),
            'view'  => Pages\ViewAuthenticityBadgeRequest::route('/{record}'),
        ];
    }

    // ── Navigation badge (pending count) ────────────────────────────

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::pending()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}

<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\MyTeamResource\Pages;
use App\Models\RestaurantTeamMember;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TeamInvitation;

class MyTeamResource extends Resource
{
    protected static ?string $model = RestaurantTeamMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Mi Equipo';

    protected static ?string $modelLabel = 'Miembro del Equipo';

    protected static ?string $pluralModelLabel = 'Miembros del Equipo';

    protected static ?string $navigationGroup = 'Configuracion';

    protected static ?int $navigationSort = 10;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        // Only restaurant owners can manage team
        return $user->restaurants()->exists();
    }

    public static function canAccess(): bool
    {
        return static::shouldRegisterNavigation();
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $restaurantIds = $user->allAccessibleRestaurants()->pluck('id');

        return parent::getEloquentQuery()
            ->whereIn('restaurant_id', $restaurantIds);
    }

    public static function canCreate(): bool
    {
        // Only owners can invite team members
        $user = Auth::user();
        if (!$user) return false;

        // Check if user is owner of any restaurant
        return $user->restaurants()->exists();
    }

    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $restaurants = $user->restaurants()->pluck('name', 'id');

        return $form
            ->schema([
                Forms\Components\Section::make('Invitar Nuevo Miembro')
                    ->schema([
                        Forms\Components\Select::make('restaurant_id')
                            ->label('Restaurante')
                            ->options($restaurants)
                            ->required()
                            ->visible(fn () => $restaurants->count() > 1)
                            ->default(fn () => $restaurants->count() === 1 ? $restaurants->keys()->first() : null),

                        Forms\Components\TextInput::make('email')
                            ->label('Email del nuevo miembro')
                            ->email()
                            ->required()
                            ->helperText('Se enviara una invitacion a este email'),

                        Forms\Components\Select::make('role')
                            ->label('Rol')
                            ->options([
                                RestaurantTeamMember::ROLE_MANAGER => 'Gerente - Acceso completo excepto configuracion',
                                RestaurantTeamMember::ROLE_EDITOR => 'Editor - Editar menu, fotos y contenido',
                            ])
                            ->required()
                            ->default(RestaurantTeamMember::ROLE_EDITOR),

                        Forms\Components\Section::make('Permisos Personalizados')
                            ->schema([
                                Forms\Components\Toggle::make('permissions.reservations')
                                    ->label('Gestionar Reservaciones')
                                    ->default(true),
                                Forms\Components\Toggle::make('permissions.reviews')
                                    ->label('Responder Resenas'),
                                Forms\Components\Toggle::make('permissions.menu')
                                    ->label('Editar Menu'),
                                Forms\Components\Toggle::make('permissions.photos')
                                    ->label('Gestionar Fotos'),
                                Forms\Components\Toggle::make('permissions.coupons')
                                    ->label('Gestionar Cupones'),
                                Forms\Components\Toggle::make('permissions.analytics')
                                    ->label('Ver Estadisticas'),
                            ])
                            ->columns(2)
                            ->collapsed()
                            ->collapsible(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nombre')
                    ->default(fn ($record) => $record->user?->name ?? 'Invitacion pendiente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->default(fn ($record) => $record->user?->email ?? '-')
                    ->copyable(),
                Tables\Columns\TextColumn::make('restaurant.name')
                    ->label('Restaurante')
                    ->visible(fn () => Auth::user()->restaurants()->count() > 1),
                Tables\Columns\TextColumn::make('role')
                    ->label('Rol')
                    ->formatStateUsing(fn ($state) => RestaurantTeamMember::getRoles()[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'manager' => 'warning',
                        'editor' => 'info',
                        'viewer' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending' => 'Invitacion Pendiente',
                        'active' => 'Activo',
                        'revoked' => 'Revocado',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'active' => 'success',
                        'revoked' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('accepted_at')
                    ->label('Fecha de union')
                    ->date('d/m/Y')
                    ->placeholder('Pendiente'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'active' => 'Activo',
                        'revoked' => 'Revocado',
                    ]),
                Tables\Filters\SelectFilter::make('role')
                    ->label('Rol')
                    ->options(RestaurantTeamMember::getRoles()),
            ])
            ->actions([
                Tables\Actions\Action::make('resend')
                    ->label('Reenviar invitacion')
                    ->icon('heroicon-o-envelope')
                    ->visible(fn ($record) => $record->status === RestaurantTeamMember::STATUS_PENDING)
                    ->action(function ($record) {
                        // Generate new token and extend expiration
                        $record->update([
                            'invitation_token' => RestaurantTeamMember::generateInvitationToken(),
                            'invitation_expires_at' => now()->addDays(7),
                        ]);

                        // Send invitation email
                        if ($record->user) {
                            try {
                                Mail::to($record->user->email)->send(new TeamInvitation($record));
                            } catch (\Exception $e) {
                                // Log error silently
                            }
                        }

                        Notification::make()
                            ->title('Invitacion reenviada')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('revoke')
                    ->label('Revocar acceso')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => in_array($record->status, [
                        RestaurantTeamMember::STATUS_PENDING,
                        RestaurantTeamMember::STATUS_ACTIVE,
                    ]) && $record->role !== RestaurantTeamMember::ROLE_ADMIN)
                    ->requiresConfirmation()
                    ->modalHeading('Revocar acceso')
                    ->modalDescription('Esta persona ya no podra acceder al panel de administracion del restaurante.')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Motivo (opcional)')
                            ->placeholder('Razon por la que se revoca el acceso'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->revoke($data['reason'] ?? null);

                        Notification::make()
                            ->title('Acceso revocado')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('restore')
                    ->label('Restaurar acceso')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === RestaurantTeamMember::STATUS_REVOKED)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status' => RestaurantTeamMember::STATUS_ACTIVE,
                            'revoked_at' => null,
                            'revoked_reason' => null,
                        ]);

                        Notification::make()
                            ->title('Acceso restaurado')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyTeam::route('/'),
            'create' => Pages\CreateMyTeam::route('/create'),
        ];
    }
}

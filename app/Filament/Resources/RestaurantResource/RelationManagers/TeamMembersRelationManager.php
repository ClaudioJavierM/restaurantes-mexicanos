<?php

namespace App\Filament\Resources\RestaurantResource\RelationManagers;

use App\Models\RestaurantTeamMember;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use App\Mail\TeamInvitation;

class TeamMembersRelationManager extends RelationManager
{
    protected static string $relationship = 'teamMembers';

    protected static ?string $title = 'Equipo';

    protected static ?string $modelLabel = 'Miembro';

    protected static ?string $pluralModelLabel = 'Miembros del Equipo';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(User::class, 'email'),
                    ])
                    ->createOptionUsing(function (array $data) {
                        $user = User::create([
                            'name' => $data['name'],
                            'email' => $data['email'],
                            'password' => bcrypt(\Str::random(16)),
                            'role' => 'user',
                        ]);
                        return $user->id;
                    }),

                Forms\Components\Select::make('role')
                    ->label('Rol')
                    ->options(RestaurantTeamMember::getRoles())
                    ->required()
                    ->default('staff'),

                Forms\Components\Select::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Invitacion Pendiente',
                        'active' => 'Activo',
                        'revoked' => 'Revocado',
                    ])
                    ->default('pending')
                    ->disabled(fn ($record) => !$record),

                Forms\Components\CheckboxList::make('permissions')
                    ->label('Permisos Personalizados')
                    ->options(RestaurantTeamMember::getPermissionsList())
                    ->columns(2)
                    ->visible(fn (Forms\Get $get) => $get('role') !== 'owner')
                    ->helperText('Deja vacio para usar permisos por defecto del rol'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->formatStateUsing(fn (RestaurantTeamMember $record) => $record->getRoleLabel())
                    ->color(fn (RestaurantTeamMember $record) => $record->getRoleColor()),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (RestaurantTeamMember $record) => $record->getStatusLabel())
                    ->color(fn (RestaurantTeamMember $record) => $record->getStatusColor()),

                Tables\Columns\TextColumn::make('accepted_at')
                    ->label('Activo desde')
                    ->dateTime('d/m/Y')
                    ->toggleable()
                    ->placeholder('Pendiente'),

                Tables\Columns\TextColumn::make('inviter.name')
                    ->label('Invitado por')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Rol')
                    ->options(RestaurantTeamMember::getRoles()),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'active' => 'Activo',
                        'revoked' => 'Revocado',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('invite')
                    ->label('Invitar Miembro')
                    ->icon('heroicon-o-user-plus')
                    ->color('primary')
                    ->form([
                        Forms\Components\TextInput::make('email')
                            ->label('Email del nuevo miembro')
                            ->email()
                            ->required()
                            ->helperText('Se enviara una invitacion por email'),

                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required(),

                        Forms\Components\Select::make('role')
                            ->label('Rol')
                            ->options([
                                'owner' => 'Propietario (acceso completo)',
                                'manager' => 'Gerente (gestiona operaciones)',
                                'staff' => 'Staff (solo reservaciones)',
                            ])
                            ->required()
                            ->default('manager'),
                    ])
                    ->action(function (array $data) {
                        $restaurant = $this->getOwnerRecord();

                        // Check if user exists
                        $user = User::where('email', $data['email'])->first();

                        if (!$user) {
                            // Create new user with random password
                            $user = User::create([
                                'name' => $data['name'],
                                'email' => $data['email'],
                                'password' => bcrypt(\Str::random(16)),
                                'role' => 'user',
                            ]);
                        }

                        // Check if already a member
                        $existingMember = $restaurant->teamMembers()
                            ->where('user_id', $user->id)
                            ->whereIn('status', ['pending', 'active'])
                            ->first();

                        if ($existingMember) {
                            Notification::make()
                                ->warning()
                                ->title('Este usuario ya es miembro del equipo')
                                ->send();
                            return;
                        }

                        // Create team member
                        $member = RestaurantTeamMember::create([
                            'restaurant_id' => $restaurant->id,
                            'user_id' => $user->id,
                            'role' => $data['role'],
                            'status' => 'pending',
                            'invited_by' => auth()->id(),
                        ]);

                        // Send invitation email
                        try {
                            Mail::to($user->email)->send(new TeamInvitation($member));
                        } catch (\Exception $e) {
                            \Log::error('Failed to send team invitation: ' . $e->getMessage());
                        }

                        Notification::make()
                            ->success()
                            ->title('Invitacion enviada')
                            ->body("Se ha enviado una invitacion a {$user->email}")
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('resend')
                    ->label('Reenviar')
                    ->icon('heroicon-o-envelope')
                    ->color('info')
                    ->visible(fn (RestaurantTeamMember $record) => $record->isPending())
                    ->action(function (RestaurantTeamMember $record) {
                        // Regenerate token and extend expiration
                        $record->update([
                            'invitation_token' => RestaurantTeamMember::generateInvitationToken(),
                            'invitation_expires_at' => now()->addDays(7),
                        ]);

                        // Send invitation email
                        try {
                            Mail::to($record->user->email)->send(new TeamInvitation($record));
                        } catch (\Exception $e) {
                            \Log::error('Failed to resend team invitation: ' . $e->getMessage());
                        }

                        Notification::make()
                            ->success()
                            ->title('Invitacion reenviada')
                            ->send();
                    }),

                Tables\Actions\Action::make('activate')
                    ->label('Activar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (RestaurantTeamMember $record) => $record->isPending())
                    ->requiresConfirmation()
                    ->action(function (RestaurantTeamMember $record) {
                        $record->accept();

                        Notification::make()
                            ->success()
                            ->title('Miembro activado')
                            ->send();
                    }),

                Tables\Actions\Action::make('revoke')
                    ->label('Revocar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (RestaurantTeamMember $record) => $record->isActive())
                    ->requiresConfirmation()
                    ->modalHeading('Revocar acceso')
                    ->modalDescription('Esta accion removera el acceso de este usuario al restaurante.')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Razon (opcional)')
                            ->maxLength(255),
                    ])
                    ->action(function (RestaurantTeamMember $record, array $data) {
                        $record->revoke($data['reason'] ?? null);

                        Notification::make()
                            ->success()
                            ->title('Acceso revocado')
                            ->send();
                    }),

                Tables\Actions\EditAction::make()
                    ->visible(fn (RestaurantTeamMember $record) => !$record->isOwner() || auth()->user()->canManageTeamFor($this->getOwnerRecord())),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn (RestaurantTeamMember $record) => !$record->isOwner()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

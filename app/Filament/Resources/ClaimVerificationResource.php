<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClaimVerificationResource\Pages;
use App\Filament\Resources\ClaimVerificationResource\RelationManagers;
use App\Models\ClaimVerification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClaimVerificationResource extends Resource
{
    protected static ?string $model = ClaimVerification::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Verificaciones de Claim';

    protected static ?string $modelLabel = 'Verificación de Claim';

    protected static ?string $pluralModelLabel = 'Verificaciones de Claims';

    protected static ?string $navigationGroup = 'Restaurantes';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Restaurante')
                    ->schema([
                        Forms\Components\Select::make('restaurant_id')
                            ->label('Restaurante')
                            ->relationship('restaurant', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),

                Forms\Components\Section::make('Información del Propietario')
                    ->schema([
                        Forms\Components\TextInput::make('owner_name')
                            ->label('Nombre del Propietario')
                            ->required(),
                        Forms\Components\TextInput::make('owner_email')
                            ->label('Email')
                            ->email()
                            ->required(),
                        Forms\Components\TextInput::make('owner_phone')
                            ->label('Teléfono')
                            ->tel(),
                    ])->columns(3),

                Forms\Components\Section::make('Verificación')
                    ->schema([
                        Forms\Components\Select::make('verification_method')
                            ->label('Método de Verificación')
                            ->options([
                                'phone' => 'Teléfono',
                                'email' => 'Email',
                                'document' => 'Documento',
                            ]),
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'pending' => 'Pendiente',
                                'verified' => 'Verificado',
                                'rejected' => 'Rechazado',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verificado'),
                        Forms\Components\DateTimePicker::make('verified_at')
                            ->label('Fecha de Verificación'),
                    ])->columns(2),

                Forms\Components\Section::make('Rechazo')
                    ->schema([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Razón del Rechazo')
                            ->rows(3),
                    ])
                    ->visible(fn (Forms\Get $get) => $get('status') === 'rejected'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('restaurant.name')
                    ->label('Restaurante')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('restaurant.city')
                    ->label('Ciudad')
                    ->sortable(),
                Tables\Columns\TextColumn::make('owner_name')
                    ->label('Propietario')
                    ->searchable(),
                Tables\Columns\TextColumn::make('owner_email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('owner_phone')
                    ->label('Teléfono')
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('verification_method')
                    ->label('Método')
                    ->colors([
                        'info' => 'phone',
                        'success' => 'email',
                        'warning' => 'document',
                    ])
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'phone' => 'Teléfono',
                        'email' => 'Email',
                        'document' => 'Documento',
                        default => $state ?? 'N/A',
                    }),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'verified',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'verified' => 'Verificado',
                        'rejected' => 'Rechazado',
                        default => $state ?? 'N/A',
                    }),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verificado')
                    ->boolean(),
                Tables\Columns\TextColumn::make('verified_at')
                    ->label('Fecha Verificación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Solicitado')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'verified' => 'Verificado',
                        'rejected' => 'Rechazado',
                    ]),
                Tables\Filters\SelectFilter::make('verification_method')
                    ->label('Método')
                    ->options([
                        'phone' => 'Teléfono',
                        'email' => 'Email',
                        'document' => 'Documento',
                    ]),
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verificado'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (ClaimVerification $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (ClaimVerification $record) {
                        $record->update([
                            'status' => 'verified',
                            'is_verified' => true,
                            'verified_at' => now(),
                        ]);
                        
                        // Marcar restaurante como reclamado
                        $record->restaurant->update(['is_claimed' => true, 'claimed_at' => now()]);
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (ClaimVerification $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Razón del Rechazo')
                            ->required(),
                    ])
                    ->action(function (ClaimVerification $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Sin solicitudes de claim')
            ->emptyStateDescription('Aún no hay solicitudes de verificación de propietarios.');
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
            'index' => Pages\ListClaimVerifications::route('/'),
            'create' => Pages\CreateClaimVerification::route('/create'),
            'edit' => Pages\EditClaimVerification::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::where('status', 'pending')->count();
        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        $totalClaimed = \App\Models\Restaurant::where('is_claimed', true)->count();
        return "Total reclamados: {$totalClaimed}";
    }
}

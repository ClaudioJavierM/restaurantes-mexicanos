<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClaimVerificationResource\Pages;
use App\Models\Restaurant;
use App\Models\ClaimVerification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClaimVerificationResource extends Resource
{
    protected static ?string $model = Restaurant::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Restaurantes Reclamados';

    protected static ?string $modelLabel = 'Restaurante Reclamado';

    protected static ?string $pluralModelLabel = 'Restaurantes Reclamados';

    protected static ?string $navigationGroup = 'Restaurantes';

    protected static ?int $navigationSort = 4;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected static ?string $slug = 'claimed-restaurants';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where(function ($q) {
                $q->where('is_claimed', true)
                  ->orWhereNotNull('user_id');
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Restaurante')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->disabled(),
                        Forms\Components\TextInput::make('city')
                            ->label('Ciudad')
                            ->disabled(),
                        Forms\Components\Select::make('subscription_tier')
                            ->label('Plan')
                            ->options([
                                'free' => 'Free',
                                'claimed' => 'Claimed',
                                'premium' => 'Premium ($29/mes)',
                                'elite' => 'Elite ($79/mes)',
                            ]),
                    ])->columns(3),

                Forms\Components\Section::make('Propietario')
                    ->schema([
                        Forms\Components\TextInput::make('owner_email')
                            ->label('Email del Dueño')
                            ->email(),
                        Forms\Components\TextInput::make('owner_phone')
                            ->label('Teléfono del Dueño')
                            ->tel(),
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario Asignado')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ])->columns(3),

                Forms\Components\Section::make('Estado del Claim')
                    ->schema([
                        Forms\Components\Toggle::make('is_claimed')
                            ->label('Reclamado'),
                        Forms\Components\DateTimePicker::make('claimed_at')
                            ->label('Fecha de Claim'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Restaurante')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Ciudad')
                    ->sortable(),
                Tables\Columns\TextColumn::make('state.name')
                    ->label('Estado')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('subscription_tier')
                    ->label('Plan')
                    ->colors([
                        'gray' => 'free',
                        'info' => 'claimed',
                        'warning' => 'premium',
                        'success' => 'elite',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'free' => 'Free',
                        'claimed' => 'Claimed',
                        'premium' => 'Premium',
                        'elite' => 'Elite',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('owner_email')
                    ->label('Email Dueño')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email Login')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_claimed')
                    ->label('Claimed')
                    ->boolean(),
                Tables\Columns\TextColumn::make('claimed_at')
                    ->label('Fecha Claim')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('average_rating')
                    ->label('Rating')
                    ->numeric(1)
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('review_count')
                    ->label('Reviews')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subscription_tier')
                    ->label('Plan')
                    ->options([
                        'free' => 'Free',
                        'claimed' => 'Claimed',
                        'premium' => 'Premium',
                        'elite' => 'Elite',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('upgrade')
                    ->label('Upgrade')
                    ->icon('heroicon-o-arrow-up-circle')
                    ->color('warning')
                    ->visible(fn (Restaurant $record) => in_array($record->subscription_tier, ['free', 'claimed']))
                    ->form([
                        Forms\Components\Select::make('new_tier')
                            ->label('Nuevo Plan')
                            ->options([
                                'claimed' => 'Claimed',
                                'premium' => 'Premium ($29/mes)',
                                'elite' => 'Elite ($79/mes)',
                            ])
                            ->required(),
                    ])
                    ->action(function (Restaurant $record, array $data) {
                        $record->update(['subscription_tier' => $data['new_tier']]);
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('claimed_at', 'desc')
            ->emptyStateHeading('Sin restaurantes reclamados')
            ->emptyStateDescription('Aún no hay restaurantes reclamados por sus dueños.');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClaimVerifications::route('/'),
            'edit' => Pages\EditClaimVerification::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Restaurant::where(function ($q) {
            $q->where('is_claimed', true)
              ->orWhereNotNull('user_id');
        })->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}

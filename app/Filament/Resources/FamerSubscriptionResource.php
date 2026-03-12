<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FamerSubscriptionResource\Pages;
use App\Models\Restaurant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FamerSubscriptionResource extends Resource
{
    protected static ?string $model = Restaurant::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Suscripciones de Pago';

    protected static ?string $modelLabel = 'Suscripción de Pago';

    protected static ?string $pluralModelLabel = 'Suscripciones de Pago';

    protected static ?string $navigationGroup = 'Restaurantes';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'subscriptions';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('subscription_tier', ['premium', 'elite']);
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
                        Forms\Components\Select::make('subscription_tier')
                            ->label('Plan')
                            ->options([
                                'premium' => 'Premium ($29/mes)',
                                'elite' => 'Elite ($79/mes)',
                            ])
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Contacto del Dueño')
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
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('subscription_tier')
                    ->label('Plan')
                    ->colors([
                        'warning' => 'premium',
                        'success' => 'elite',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'premium' => 'Premium ($29/mes)',
                        'elite' => 'Elite ($79/mes)',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('owner_email')
                    ->label('Email Dueño')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('claimed_at')
                    ->label('Fecha Claim')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('average_rating')
                    ->label('Rating')
                    ->numeric(1)
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subscription_tier')
                    ->label('Plan')
                    ->options([
                        'premium' => 'Premium',
                        'elite' => 'Elite',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('subscription_tier', 'desc')
            ->emptyStateHeading('Sin suscripciones de pago')
            ->emptyStateDescription('Aún no hay restaurantes con plan Premium o Elite.');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFamerSubscriptions::route('/'),
            'edit' => Pages\EditFamerSubscription::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Restaurant::whereIn('subscription_tier', ['premium', 'elite'])->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}

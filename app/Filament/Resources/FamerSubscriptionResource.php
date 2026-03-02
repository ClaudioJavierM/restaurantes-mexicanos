<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FamerSubscriptionResource\Pages;
use App\Filament\Resources\FamerSubscriptionResource\RelationManagers;
use App\Models\FamerSubscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FamerSubscriptionResource extends Resource
{
    protected static ?string $model = FamerSubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Suscripciones';

    protected static ?string $modelLabel = 'Suscripción';

    protected static ?string $pluralModelLabel = 'Suscripciones';

    protected static ?string $navigationGroup = 'Restaurantes';

    protected static ?int $navigationSort = 3;

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
                        Forms\Components\Select::make('user_id')
                            ->label('Usuario/Dueño')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                Forms\Components\Section::make('Plan')
                    ->schema([
                        Forms\Components\TextInput::make('year')
                            ->label('Año')
                            ->required()
                            ->numeric()
                            ->default(date('Y')),
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'active' => 'Activo',
                                'pending' => 'Pendiente',
                                'cancelled' => 'Cancelado',
                                'expired' => 'Expirado',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\DateTimePicker::make('subscribed_at')
                            ->label('Fecha de Suscripción'),
                    ])->columns(3),

                Forms\Components\Section::make('Contacto')
                    ->schema([
                        Forms\Components\TextInput::make('contact_email')
                            ->label('Email de Contacto')
                            ->email(),
                        Forms\Components\TextInput::make('contact_phone')
                            ->label('Teléfono de Contacto')
                            ->tel(),
                    ])->columns(2),

                Forms\Components\Section::make('Preferencias')
                    ->schema([
                        Forms\Components\Toggle::make('wants_notifications')
                            ->label('Quiere Notificaciones')
                            ->default(true),
                        Forms\Components\Toggle::make('allows_promotion')
                            ->label('Permite Promoción')
                            ->default(true),
                        Forms\Components\Textarea::make('goals')
                            ->label('Objetivos/Notas')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
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
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Dueño')
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                    ->label('Año')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending',
                        'danger' => 'cancelled',
                        'gray' => 'expired',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Activo',
                        'pending' => 'Pendiente',
                        'cancelled' => 'Cancelado',
                        'expired' => 'Expirado',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('wants_notifications')
                    ->label('Notif.')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('allows_promotion')
                    ->label('Promo')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('contact_email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('subscribed_at')
                    ->label('Fecha Suscripción')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activo',
                        'pending' => 'Pendiente',
                        'cancelled' => 'Cancelado',
                        'expired' => 'Expirado',
                    ]),
                Tables\Filters\SelectFilter::make('year')
                    ->label('Año')
                    ->options([
                        '2024' => '2024',
                        '2025' => '2025',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('subscribed_at', 'desc')
            ->emptyStateHeading('Sin suscripciones')
            ->emptyStateDescription('Aún no hay restaurantes suscritos a Famer.');
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
            'index' => Pages\ListFamerSubscriptions::route('/'),
            'create' => Pages\CreateFamerSubscription::route('/create'),
            'edit' => Pages\EditFamerSubscription::route('/{record}/edit'),
        ];
    }
}

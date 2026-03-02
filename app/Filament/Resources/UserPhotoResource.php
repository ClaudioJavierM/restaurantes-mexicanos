<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserPhotoResource\Pages;
use App\Models\UserPhoto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserPhotoResource extends Resource
{
    protected static ?string $model = UserPhoto::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Fotos de Usuarios';

    protected static ?string $modelLabel = 'Foto';

    protected static ?string $pluralModelLabel = 'Fotos de Usuarios';

    protected static ?string $navigationGroup = 'Contenido';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::where('status', 'pending')->count() > 0 ? 'warning' : 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informacion de la Foto')
                    ->schema([
                        Forms\Components\Select::make('restaurant_id')
                            ->relationship('restaurant', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('photo_type')
                            ->label('Tipo de Foto')
                            ->options([
                                'food' => 'Comida',
                                'interior' => 'Interior',
                                'exterior' => 'Exterior',
                                'menu' => 'Menu',
                                'drink' => 'Bebidas',
                                'other' => 'Otro',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('caption')
                            ->label('Descripcion')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Estado')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'pending' => 'Pendiente',
                                'approved' => 'Aprobada',
                                'rejected' => 'Rechazada',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Razon de Rechazo')
                            ->visible(fn (Forms\Get $get) => $get('status') === 'rejected')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo_path')
                    ->label('Foto')
                    ->disk('public')
                    ->width(80)
                    ->height(80)
                    ->square(),
                Tables\Columns\TextColumn::make('restaurant.name')
                    ->label('Restaurante')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->default('Anonimo'),
                Tables\Columns\TextColumn::make('photo_type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'food' => 'Comida',
                        'interior' => 'Interior',
                        'exterior' => 'Exterior',
                        'menu' => 'Menu',
                        'drink' => 'Bebidas',
                        default => 'Otro',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'food' => 'success',
                        'interior' => 'info',
                        'exterior' => 'warning',
                        'menu' => 'primary',
                        'drink' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobada',
                        'rejected' => 'Rechazada',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('likes_count')
                    ->label('Likes')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Vistas')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('reports_count')
                    ->label('Reportes')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'approved' => 'Aprobada',
                        'rejected' => 'Rechazada',
                    ]),
                Tables\Filters\SelectFilter::make('photo_type')
                    ->label('Tipo')
                    ->options([
                        'food' => 'Comida',
                        'interior' => 'Interior',
                        'exterior' => 'Exterior',
                        'menu' => 'Menu',
                        'drink' => 'Bebidas',
                        'other' => 'Otro',
                    ]),
                Tables\Filters\Filter::make('has_reports')
                    ->label('Con reportes')
                    ->query(fn (Builder $query): Builder => $query->where('reports_count', '>', 0)),
            ])
            ->actions([
                Tables\Actions\Action::make('view_photo')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Vista Previa')
                    ->modalContent(fn (UserPhoto $record) => view('filament.resources.user-photo-resource.view-photo', ['photo' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),
                Tables\Actions\Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (UserPhoto $record) => $record->status !== 'approved')
                    ->requiresConfirmation()
                    ->modalHeading('Aprobar Foto')
                    ->modalDescription('Esta seguro que desea aprobar esta foto?')
                    ->action(function (UserPhoto $record) {
                        $record->approve(auth()->id());

                        Notification::make()
                            ->success()
                            ->title('Foto Aprobada')
                            ->body('La foto ha sido aprobada exitosamente.')
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (UserPhoto $record) => $record->status !== 'rejected')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Razon del rechazo')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(function (UserPhoto $record, array $data) {
                        $record->reject($data['reason'], auth()->id());

                        Notification::make()
                            ->success()
                            ->title('Foto Rechazada')
                            ->body('La foto ha sido rechazada.')
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Aprobar Seleccionadas')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(fn (UserPhoto $record) => $record->approve(auth()->id()));

                            Notification::make()
                                ->success()
                                ->title('Fotos Aprobadas')
                                ->body(count($records) . ' fotos han sido aprobadas.')
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('reject_selected')
                        ->label('Rechazar Seleccionadas')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->label('Razon del rechazo')
                                ->required()
                                ->maxLength(500),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each(fn (UserPhoto $record) => $record->reject($data['reason'], auth()->id()));

                            Notification::make()
                                ->success()
                                ->title('Fotos Rechazadas')
                                ->body(count($records) . ' fotos han sido rechazadas.')
                                ->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListUserPhotos::route('/'),
        ];
    }
}

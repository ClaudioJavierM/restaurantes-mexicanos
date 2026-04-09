<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RestaurantReportResource\Pages;
use App\Models\RestaurantReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class RestaurantReportResource extends Resource
{
    protected static ?string $model = RestaurantReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationLabel = 'Reportes';

    protected static ?string $modelLabel = 'Reporte';

    protected static ?string $pluralModelLabel = 'Reportes';

    protected static ?string $navigationGroup = 'Restaurantes';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Reporte')
                    ->schema([
                        Forms\Components\Select::make('restaurant_id')
                            ->label('Restaurante')
                            ->relationship('restaurant', 'name')
                            ->searchable()
                            ->required()
                            ->disabled(),

                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Reporter')
                            ->disabled(),

                        Forms\Components\TextInput::make('email')
                            ->label('Email del Reporter')
                            ->email()
                            ->disabled(),

                        Forms\Components\Select::make('issue_type')
                            ->label('Tipo de Problema')
                            ->options(RestaurantReport::getIssueTypes())
                            ->required()
                            ->disabled(),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(4)
                            ->required()
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Gestión del Reporte')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options(RestaurantReport::getStatuses())
                            ->required()
                            ->default('pending'),

                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Notas del Admin')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\DateTimePicker::make('resolved_at')
                            ->label('Resuelto el')
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('restaurant.name')
                    ->label('Restaurante')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->url(fn($record) => route('restaurants.show', $record->restaurant)),

                Tables\Columns\TextColumn::make('issue_type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn($state) => RestaurantReport::getIssueTypes()[$state] ?? $state)
                    ->color(fn($state) => match($state) {
                        'incorrect_info' => 'warning',
                        'closed' => 'danger',
                        'duplicate' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Reporter')
                    ->searchable()
                    ->toggleable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn($state) => RestaurantReport::getStatuses()[$state] ?? $state)
                    ->color(fn($state) => match($state) {
                        'pending' => 'warning',
                        'reviewed' => 'info',
                        'resolved' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Reportado')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(RestaurantReport::getStatuses())
                    ->multiple(),

                SelectFilter::make('issue_type')
                    ->label('Tipo de Problema')
                    ->options(RestaurantReport::getIssueTypes())
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('mark_resolved')
                    ->label('Marcar como Resuelto')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (RestaurantReport $record) => $record->markAsResolved())
                    ->visible(fn (RestaurantReport $record) => !$record->isResolved()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListRestaurantReports::route('/'),
            'edit' => Pages\EditRestaurantReport::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}

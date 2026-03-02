<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\MyCouponsResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class MyCouponsResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    
    protected static ?string $navigationLabel = 'Cupones';
    
    protected static ?string $modelLabel = 'Cupón';
    
    protected static ?string $pluralModelLabel = 'Cupones';

    protected static ?string $navigationGroup = 'Mi Negocio';
    
    protected static ?int $navigationSort = 5;

    public static function getEloquentQuery(): Builder
    {
        $restaurantIds = auth()->user()->restaurants()->pluck('id');
        
        return parent::getEloquentQuery()
            ->whereIn('restaurant_id', $restaurantIds);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Cupón')
                    ->schema([
                        Forms\Components\Hidden::make('restaurant_id')
                            ->default(fn () => auth()->user()->restaurants()->first()?->id),

                        Forms\Components\TextInput::make('title')
                            ->label('Título del Cupón')
                            ->placeholder('Ej: 20% de descuento en tu primera orden')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('title_en')
                            ->label('Título en Inglés')
                            ->placeholder('Ej: 20% off your first order')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('code')
                            ->label('Código del Cupón')
                            ->placeholder('DESCUENTO20')
                            ->required()
                            ->maxLength(50)
                            ->default(fn () => strtoupper(Str::random(8)))
                            ->helperText('Los clientes usarán este código'),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(2)
                            ->placeholder('Describe los detalles del cupón...'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Descuento')
                    ->schema([
                        Forms\Components\Select::make('discount_type')
                            ->label('Tipo de Descuento')
                            ->options([
                                'percentage' => 'Porcentaje (%)',
                                'fixed' => 'Monto Fijo ($)',
                            ])
                            ->required()
                            ->default('percentage'),

                        Forms\Components\TextInput::make('discount_value')
                            ->label('Valor del Descuento')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->default(10),

                        Forms\Components\TextInput::make('minimum_purchase')
                            ->label('Compra Mínima ($)')
                            ->numeric()
                            ->default(0)
                            ->helperText('0 = sin mínimo'),

                        Forms\Components\TextInput::make('maximum_discount')
                            ->label('Descuento Máximo ($)')
                            ->numeric()
                            ->helperText('Solo para descuentos porcentuales'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Validez')
                    ->schema([
                        Forms\Components\DatePicker::make('valid_from')
                            ->label('Válido Desde')
                            ->default(now()),

                        Forms\Components\DatePicker::make('valid_until')
                            ->label('Válido Hasta')
                            ->default(now()->addMonth()),

                        Forms\Components\TextInput::make('usage_limit')
                            ->label('Límite de Usos')
                            ->numeric()
                            ->helperText('Vacío = sin límite'),

                        Forms\Components\TextInput::make('usage_limit_per_user')
                            ->label('Límite por Usuario')
                            ->numeric()
                            ->default(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Aplicable a')
                    ->schema([
                        Forms\Components\Toggle::make('applicable_dine_in')
                            ->label('Comer en restaurante')
                            ->default(true),

                        Forms\Components\Toggle::make('applicable_takeout')
                            ->label('Para llevar')
                            ->default(true),

                        Forms\Components\Toggle::make('applicable_delivery')
                            ->label('Delivery')
                            ->default(true),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Estado')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),

                        Forms\Components\Toggle::make('is_featured')
                            ->label('Destacado')
                            ->helperText('Se mostrará en la página del restaurante'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Términos y Condiciones')
                    ->schema([
                        Forms\Components\Textarea::make('terms')
                            ->label('Términos (Español)')
                            ->rows(3)
                            ->placeholder('No válido con otras promociones...'),

                        Forms\Components\Textarea::make('terms_en')
                            ->label('Términos (Inglés)')
                            ->rows(3),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Cupón')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->badge()
                    ->copyable()
                    ->copyMessage('Código copiado'),

                Tables\Columns\TextColumn::make('formatted_discount')
                    ->label('Descuento')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('valid_until')
                    ->label('Expira')
                    ->date('d M, Y')
                    ->color(fn (Coupon $record): string => $record->is_expired ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('usage_count')
                    ->label('Usos')
                    ->formatStateUsing(fn (Coupon $record): string => 
                        $record->usage_limit 
                            ? $record->usage_count . '/' . $record->usage_limit 
                            : (string) $record->usage_count
                    ),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Estado')
                    ->options([
                        true => 'Activos',
                        false => 'Inactivos',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle')
                    ->label(fn (Coupon $record): string => $record->is_active ? 'Desactivar' : 'Activar')
                    ->icon(fn (Coupon $record): string => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Coupon $record): string => $record->is_active ? 'danger' : 'success')
                    ->action(fn (Coupon $record) => $record->update(['is_active' => !$record->is_active]))
                    ->requiresConfirmation(),
                Tables\Actions\DeleteAction::make(),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyCoupons::route('/'),
            'create' => Pages\CreateMyCoupon::route('/create'),
            'edit' => Pages\EditMyCoupon::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        $restaurant = $user->restaurants()->first();
        if (!$restaurant) return false;
        
        return in_array($restaurant->subscription_tier, ["premium", "elite"]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        $restaurant = $user->restaurants()->first();
        return $restaurant && $restaurant->is_claimed;
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        if (!$user) return null;
        
        $restaurant = $user->restaurants()->first();
        if ($restaurant && !in_array($restaurant->subscription_tier, ["premium", "elite"])) {
            return "PRO";
        }
        
        $restaurantIds = $user->restaurants()->pluck("id");
        $count = \App\Models\Coupon::whereIn("restaurant_id", $restaurantIds)
            ->where("is_active", true)
            ->valid()
            ->count();
        
        return $count > 0 ? (string) $count : null;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        $restaurantIds = auth()->user()?->restaurants()?->pluck("id") ?? collect();
        $restaurant = auth()->user()?->restaurants()->first();
        
        if ($restaurant && !in_array($restaurant->subscription_tier, ["premium", "elite"])) {
            return "warning";
        }
        return "success";
    }
}

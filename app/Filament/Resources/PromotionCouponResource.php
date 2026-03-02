<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromotionCouponResource\Pages;
use App\Models\PromotionCoupon;
use App\Services\StripeService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class PromotionCouponResource extends Resource
{
    protected static ?string $model = PromotionCoupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Cupones';

    protected static ?string $modelLabel = 'Cupón';

    protected static ?string $pluralModelLabel = 'Cupones de Promoción';

    protected static ?string $navigationGroup = 'Suscripciones';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Cupón')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Código')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('LAUNCH50, BLACKFRIDAY, etc.')
                            ->helperText('Código que los usuarios ingresarán (se convertirá a mayúsculas)')
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('code', strtoupper($state)))
                            ->reactive()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Lanzamiento 2025')
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->maxLength(500)
                            ->rows(3)
                            ->placeholder('Describe el propósito de esta promoción')
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Configuración del Descuento')
                    ->schema([
                        Forms\Components\Select::make('discount_type')
                            ->label('Tipo de Descuento')
                            ->required()
                            ->options([
                                'percentage' => 'Porcentaje (%)',
                                'fixed' => 'Monto Fijo ($)',
                            ])
                            ->reactive()
                            ->default('percentage')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('discount_value')
                            ->label(fn (Forms\Get $get) => $get('discount_type') === 'percentage' ? 'Porcentaje de Descuento' : 'Monto de Descuento ($)')
                            ->required()
                            ->numeric()
                            ->suffix(fn (Forms\Get $get) => $get('discount_type') === 'percentage' ? '%' : '$')
                            ->minValue(0)
                            ->maxValue(fn (Forms\Get $get) => $get('discount_type') === 'percentage' ? 100 : 999)
                            ->placeholder(fn (Forms\Get $get) => $get('discount_type') === 'percentage' ? '50' : '20')
                            ->helperText(fn (Forms\Get $get) => $get('discount_type') === 'percentage' ? 'Porcentaje (0-100)' : 'Dólares')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Duración del Descuento')
                    ->schema([
                        Forms\Components\Select::make('duration')
                            ->label('Duración')
                            ->required()
                            ->options([
                                'once' => 'Una vez (solo primer pago)',
                                'repeating' => 'Recurrente (X meses)',
                                'forever' => 'Para siempre',
                            ])
                            ->default('once')
                            ->reactive()
                            ->helperText('Cuánto tiempo aplica el descuento')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('duration_in_months')
                            ->label('Meses de duración')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(24)
                            ->visible(fn (Forms\Get $get) => $get('duration') === 'repeating')
                            ->required(fn (Forms\Get $get) => $get('duration') === 'repeating')
                            ->helperText('Cuántos meses aplica el descuento')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Límites y Restricciones')
                    ->schema([
                        Forms\Components\TextInput::make('max_redemptions')
                            ->label('Máximo de Usos')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('Ilimitado')
                            ->helperText('Déjalo vacío para usos ilimitados')
                            ->columnSpan(1),

                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Fecha de Expiración')
                            ->placeholder('Sin expiración')
                            ->helperText('Déjalo vacío si no expira')
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true)
                            ->helperText('Si el cupón está disponible para usar')
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('IDs de Stripe (Generados Automáticamente)')
                    ->schema([
                        Forms\Components\TextInput::make('stripe_coupon_id')
                            ->label('Stripe Coupon ID')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('stripe_promotion_code_id')
                            ->label('Stripe Promotion Code ID')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('times_redeemed')
                            ->label('Veces Usado')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(0)
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->hidden(fn ($record) => !$record),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Código copiado')
                    ->weight('bold')
                    ->size('lg')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('discount')
                    ->label('Descuento')
                    ->getStateUsing(function (PromotionCoupon $record) {
                        if ($record->discount_type === 'percentage') {
                            return "{$record->discount_value}% OFF";
                        }
                        return "\${$record->discount_value} OFF";
                    })
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('duration')
                    ->label('Duración')
                    ->formatStateUsing(function ($state, PromotionCoupon $record) {
                        return match($state) {
                            'once' => 'Una vez',
                            'repeating' => "{$record->duration_in_months} meses",
                            'forever' => 'Para siempre',
                        };
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'once' => 'info',
                        'repeating' => 'warning',
                        'forever' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('usage')
                    ->label('Uso')
                    ->getStateUsing(function (PromotionCoupon $record) {
                        if (!$record->max_redemptions) {
                            return "{$record->times_redeemed} / ∞";
                        }
                        return "{$record->times_redeemed} / {$record->max_redemptions}";
                    })
                    ->description(fn (PromotionCoupon $record) => $record->max_redemptions ?
                        round(($record->times_redeemed / $record->max_redemptions) * 100, 1) . '% usado' :
                        null
                    ),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expira')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Sin expiración')
                    ->description(fn (PromotionCoupon $record) =>
                        $record->expires_at ?
                        ($record->expires_at->isPast() ? '⚠️ Expirado' : '✓ Válido') :
                        null
                    ),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('discount_type')
                    ->label('Tipo')
                    ->options([
                        'percentage' => 'Porcentaje',
                        'fixed' => 'Fijo',
                    ]),

                Tables\Filters\SelectFilter::make('duration')
                    ->label('Duración')
                    ->options([
                        'once' => 'Una vez',
                        'repeating' => 'Recurrente',
                        'forever' => 'Para siempre',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Activo')
                    ->placeholder('Todos')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),

                Tables\Filters\Filter::make('expires_at')
                    ->label('Vigentes')
                    ->query(fn ($query) => $query->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    })),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('sync_stripe')
                    ->label('Sincronizar Stripe')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->visible(fn (PromotionCoupon $record) => !$record->stripe_coupon_id)
                    ->action(function (PromotionCoupon $record) {
                        try {
                            $stripeService = new StripeService();

                            // Create coupon data
                            $couponData = [
                                'duration' => $record->duration,
                                'name' => $record->code,
                            ];

                            if ($record->discount_type === 'percentage') {
                                $couponData['percent_off'] = (float) $record->discount_value;
                            } else {
                                $couponData['amount_off'] = (float) $record->discount_value * 100;
                                $couponData['currency'] = 'usd';
                            }

                            if ($record->duration === 'repeating') {
                                $couponData['duration_in_months'] = $record->duration_in_months;
                            }

                            if ($record->max_redemptions) {
                                $couponData['max_redemptions'] = $record->max_redemptions;
                            }

                            // Create in Stripe
                            $coupon = $stripeService->createCoupon($couponData);

                            $promotionOptions = [];
                            if ($record->max_redemptions) {
                                $promotionOptions['max_redemptions'] = $record->max_redemptions;
                            }
                            if ($record->expires_at) {
                                $promotionOptions['expires_at'] = $record->expires_at->timestamp;
                            }

                            $promotionCode = $stripeService->createPromotionCode($coupon->id, $record->code, $promotionOptions);

                            // Update record
                            $record->update([
                                'stripe_coupon_id' => $coupon->id,
                                'stripe_promotion_code_id' => $promotionCode->id,
                            ]);

                            Notification::make()
                                ->title('¡Sincronizado!')
                                ->body('El cupón se creó exitosamente en Stripe')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body('Error al sincronizar con Stripe: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('copy_code')
                    ->label('Copiar')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('gray')
                    ->action(fn (PromotionCoupon $record) =>
                        Notification::make()
                            ->title('Código copiado')
                            ->body($record->code)
                            ->success()
                            ->send()
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activar')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Desactivar')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
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
            'index' => Pages\ListPromotionCoupons::route('/'),
            'create' => Pages\CreatePromotionCoupon::route('/create'),
            'edit' => Pages\EditPromotionCoupon::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}

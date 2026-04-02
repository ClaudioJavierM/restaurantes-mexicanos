<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\MyReviewsResource\Pages;
use App\Models\Review;
use App\Services\AiReviewResponseService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyReviewsResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    
    protected static ?string $navigationLabel = 'Reseñas';
    
    protected static ?string $modelLabel = 'Reseña';
    
    protected static ?string $pluralModelLabel = 'Reseñas';

    protected static ?string $navigationGroup = 'Mi Negocio';
    
    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        $restaurantIds = auth()->user()->restaurants()->pluck('id');
        
        return parent::getEloquentQuery()
            ->whereIn('restaurant_id', $restaurantIds)
            ->where('status', 'approved')
            ->latest();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Reseña')
                    ->schema([
                        Forms\Components\Placeholder::make('reviewer_name')
                            ->label('Cliente')
                            ->content(fn (Review $record): string => $record->reviewer_name),
                        
                        Forms\Components\Placeholder::make('rating_display')
                            ->label('Calificación')
                            ->content(fn (Review $record): string => str_repeat('⭐', $record->rating) . ' (' . $record->rating . '/5)'),
                        
                        Forms\Components\Placeholder::make('comment')
                            ->label('Comentario del Cliente')
                            ->content(fn (Review $record): string => $record->comment ?? 'Sin comentario'),
                        
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Fecha')
                            ->content(fn (Review $record): string => $record->created_at->format('d M, Y H:i')),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Tu Respuesta')
                    ->description('Responde profesionalmente a esta reseña. Tu respuesta será visible públicamente.')
                    ->schema([
                        Forms\Components\Textarea::make('owner_response')
                            ->label('Respuesta del Propietario')
                            ->placeholder('Gracias por tu reseña. Apreciamos mucho tus comentarios...')
                            ->rows(5)
                            ->maxLength(1000)
                            ->helperText('Máximo 1000 caracteres. Sé profesional y agradecido.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('restaurant.name')
                    ->label('Restaurante')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('reviewer_name')
                    ->label('Cliente')
                    ->searchable(),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Calificación')
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', $state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('comment')
                    ->label('Comentario')
                    ->limit(50)
                    ->tooltip(fn (Review $record): ?string => $record->comment)
                    ->searchable(),

                Tables\Columns\IconColumn::make('has_response')
                    ->label('Respondida')
                    ->boolean()
                    ->getStateUsing(fn (Review $record): bool => !empty($record->owner_response))
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d M, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->label('Calificación')
                    ->options([
                        5 => '⭐⭐⭐⭐⭐ (5)',
                        4 => '⭐⭐⭐⭐ (4)',
                        3 => '⭐⭐⭐ (3)',
                        2 => '⭐⭐ (2)',
                        1 => '⭐ (1)',
                    ]),
                    
                Tables\Filters\TernaryFilter::make('has_response')
                    ->label('Respondida')
                    ->placeholder('Todas')
                    ->trueLabel('Con respuesta')
                    ->falseLabel('Sin respuesta')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('owner_response'),
                        false: fn (Builder $query) => $query->whereNull('owner_response'),
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('ai_suggest')
                    ->label('✨ Sugerir con IA')
                    ->icon('heroicon-o-sparkles')
                    ->color('warning')
                    ->visible(fn (Review $record): bool => empty($record->owner_response))
                    ->mountUsing(function (Forms\ComponentContainer $form, Review $record): void {
                        $suggestion = '';

                        try {
                            $service = new AiReviewResponseService();
                            $suggestion = $service->suggestResponse($record);
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('No se pudo generar sugerencia')
                                ->body('Revisa tu conexión o la clave de Anthropic e intenta de nuevo.')
                                ->warning()
                                ->send();
                        }

                        $form->fill(['suggestion' => $suggestion]);
                    })
                    ->form([
                        Forms\Components\Textarea::make('suggestion')
                            ->label('Respuesta sugerida por IA')
                            ->helperText('Puedes editar el texto antes de guardar.')
                            ->rows(5)
                            ->maxLength(1000)
                            ->required(),
                    ])
                    ->action(function (Review $record, array $data): void {
                        $record->update([
                            'owner_response'    => $data['suggestion'],
                            'owner_response_by' => auth()->id(),
                            'owner_response_at' => now(),
                        ]);
                    })
                    ->modalHeading('Sugerencia de respuesta con IA')
                    ->modalSubmitActionLabel('Guardar Respuesta')
                    ->successNotificationTitle('Respuesta guardada correctamente'),

                Tables\Actions\Action::make('respond')
                    ->label('Responder')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('primary')
                    ->visible(fn (Review $record): bool => empty($record->owner_response))
                    ->form([
                        Forms\Components\Textarea::make('owner_response')
                            ->label('Tu Respuesta')
                            ->placeholder('Gracias por tu reseña...')
                            ->rows(4)
                            ->required()
                            ->maxLength(1000),
                    ])
                    ->action(function (Review $record, array $data): void {
                        $record->update([
                            'owner_response' => $data['owner_response'],
                            'owner_response_by' => auth()->id(),
                            'owner_response_at' => now(),
                        ]);
                    })
                    ->successNotificationTitle('Respuesta guardada correctamente'),

                Tables\Actions\Action::make('edit_response')
                    ->label('Editar Respuesta')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->visible(fn (Review $record): bool => !empty($record->owner_response))
                    ->form([
                        Forms\Components\Textarea::make('owner_response')
                            ->label('Tu Respuesta')
                            ->default(fn (Review $record): ?string => $record->owner_response)
                            ->rows(4)
                            ->required()
                            ->maxLength(1000),
                    ])
                    ->action(function (Review $record, array $data): void {
                        $record->update([
                            'owner_response' => $data['owner_response'],
                            'owner_response_by' => auth()->id(),
                            'owner_response_at' => now(),
                        ]);
                    })
                    ->successNotificationTitle('Respuesta actualizada'),

                Tables\Actions\Action::make('view_public')
                    ->label('Ver en Sitio')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (Review $record): string => route('restaurants.show', $record->restaurant->slug) . '#reviews')
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc')
            ->poll('60s');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyReviews::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $restaurantIds = auth()->user()?->restaurants()?->pluck('id') ?? collect();
        
        $count = Review::whereIn('restaurant_id', $restaurantIds)
            ->where('status', 'approved')
            ->whereNull('owner_response')
            ->count();
        
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}

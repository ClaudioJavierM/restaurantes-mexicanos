<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\MyQuestionsResource\Pages;
use App\Models\RestaurantQuestion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyQuestionsResource extends Resource
{
    protected static ?string $model = RestaurantQuestion::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationLabel = 'Preguntas del Público';
    protected static ?string $modelLabel = 'Pregunta';
    protected static ?string $pluralModelLabel = 'Preguntas';
    protected static ?string $navigationGroup = 'Mi Negocio';
    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        $restaurantIds = auth()->user()->allAccessibleRestaurants()->pluck('id');

        return parent::getEloquentQuery()
            ->whereIn('restaurant_id', $restaurantIds)
            ->latest();
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        return $user->allAccessibleRestaurants()->exists();
    }

    public static function canAccess(): bool
    {
        return static::shouldRegisterNavigation();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('display_name')
                    ->label('Cliente')
                    ->getStateUsing(fn (RestaurantQuestion $record): string => $record->display_name)
                    ->searchable(query: fn (Builder $query, string $search) =>
                        $query->where('author_name', 'like', "%{$search}%")),

                Tables\Columns\TextColumn::make('question')
                    ->label('Pregunta')
                    ->limit(60)
                    ->tooltip(fn (RestaurantQuestion $record): string => $record->question)
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_answered')
                    ->label('Respondida')
                    ->boolean()
                    ->getStateUsing(fn (RestaurantQuestion $record): bool => !empty($record->answer))
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Aprobada')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('warning'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d M, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('answered')
                    ->label('Respondida')
                    ->placeholder('Todas')
                    ->trueLabel('Con respuesta')
                    ->falseLabel('Sin respuesta')
                    ->queries(
                        true: fn (Builder $q) => $q->whereNotNull('answer'),
                        false: fn (Builder $q) => $q->whereNull('answer'),
                    ),
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Aprobada')
                    ->placeholder('Todas')
                    ->trueLabel('Aprobadas')
                    ->falseLabel('Pendientes de aprobacion'),
            ])
            ->actions([
                Tables\Actions\Action::make('answer')
                    ->label('Responder')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('primary')
                    ->visible(fn (RestaurantQuestion $record): bool => empty($record->answer))
                    ->form([
                        Forms\Components\Placeholder::make('question_text')
                            ->label('Pregunta')
                            ->content(fn (RestaurantQuestion $record): string => $record->question),
                        Forms\Components\Textarea::make('answer')
                            ->label('Tu Respuesta')
                            ->placeholder('Responde la pregunta del cliente...')
                            ->rows(4)
                            ->required()
                            ->maxLength(1000),
                        Forms\Components\Toggle::make('is_approved')
                            ->label('Aprobar y publicar pregunta')
                            ->default(true)
                            ->helperText('Si está activo, la pregunta y respuesta serán visibles en el perfil público.'),
                    ])
                    ->action(function (RestaurantQuestion $record, array $data): void {
                        $record->update([
                            'answer' => $data['answer'],
                            'answered_by' => auth()->id(),
                            'answered_at' => now(),
                            'is_approved' => $data['is_approved'],
                        ]);
                        Notification::make()->title('Respuesta guardada')->success()->send();
                    }),

                Tables\Actions\Action::make('edit_answer')
                    ->label('Editar Respuesta')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->visible(fn (RestaurantQuestion $record): bool => !empty($record->answer))
                    ->form([
                        Forms\Components\Placeholder::make('question_text')
                            ->label('Pregunta')
                            ->content(fn (RestaurantQuestion $record): string => $record->question),
                        Forms\Components\Textarea::make('answer')
                            ->label('Respuesta')
                            ->default(fn (RestaurantQuestion $record): ?string => $record->answer)
                            ->rows(4)
                            ->required()
                            ->maxLength(1000),
                        Forms\Components\Toggle::make('is_approved')
                            ->label('Publicar pregunta')
                            ->default(fn (RestaurantQuestion $record): bool => $record->is_approved),
                    ])
                    ->action(function (RestaurantQuestion $record, array $data): void {
                        $record->update([
                            'answer' => $data['answer'],
                            'is_approved' => $data['is_approved'],
                        ]);
                        Notification::make()->title('Respuesta actualizada')->success()->send();
                    }),

                Tables\Actions\Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (RestaurantQuestion $record): bool => !$record->is_approved && !empty($record->answer))
                    ->action(function (RestaurantQuestion $record): void {
                        $record->update(['is_approved' => true]);
                        Notification::make()->title('Pregunta aprobada y publicada')->success()->send();
                    }),

                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyQuestions::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $restaurantIds = auth()->user()?->allAccessibleRestaurants()?->pluck('id') ?? collect();

        $count = RestaurantQuestion::whereIn('restaurant_id', $restaurantIds)
            ->whereNull('answer')
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}

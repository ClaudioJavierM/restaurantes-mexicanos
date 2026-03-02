<?php

namespace App\Filament\Owner\Widgets;

use App\Models\Review;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentReviewsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        $restaurantIds = auth()->user()->restaurants()->pluck('id');

        return $table
            ->query(
                Review::query()
                    ->whereIn('restaurant_id', $restaurantIds)
                    ->where('status', 'approved')
                    ->latest()
                    ->limit(5)
            )
            ->heading('Reseñas Recientes')
            ->columns([
                Tables\Columns\TextColumn::make('restaurant.name')
                    ->label('Restaurante')
                    ->sortable(),

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
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d M, Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Ver')
                    ->url(fn (Review $record): string => route('restaurants.show', $record->restaurant->slug))
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-eye'),
            ]);
    }
}

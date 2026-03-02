<?php

namespace App\Filament\Resources\RestaurantResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewsRelationManager extends RelationManager
{
    protected static string $relationship = "reviews";
    protected static ?string $title = "Resenas";
    protected static ?string $recordTitleAttribute = "title";

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute("title")
            ->columns([
                Tables\Columns\TextColumn::make("reviewer_name")
                    ->label("Revisor")
                    ->searchable(),
                Tables\Columns\TextColumn::make("rating")
                    ->label("Rating")
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state >= 4 => "success",
                        $state >= 3 => "warning",
                        default => "danger",
                    }),
                Tables\Columns\TextColumn::make("title")
                    ->label("Titulo")
                    ->limit(30)
                    ->searchable(),
                Tables\Columns\TextColumn::make("status")
                    ->label("Estado")
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        "approved" => "success",
                        "pending" => "warning",
                        "rejected" => "danger",
                        default => "gray",
                    }),
                Tables\Columns\TextColumn::make("helpful_count")
                    ->label("Util")
                    ->sortable(),
                Tables\Columns\TextColumn::make("created_at")
                    ->label("Fecha")
                    ->dateTime("d/m/Y")
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make("status")
                    ->options([
                        "approved" => "Aprobado",
                        "pending" => "Pendiente",
                        "rejected" => "Rechazado",
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make("approve")
                    ->label("Aprobar")
                    ->icon("heroicon-o-check")
                    ->color("success")
                    ->action(fn ($record) => $record->update(["status" => "approved"]))
                    ->visible(fn ($record) => $record->status !== "approved"),
            ])
            ->defaultSort("created_at", "desc");
    }
}

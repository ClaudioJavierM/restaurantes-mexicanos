<?php

namespace App\Filament\Resources\RestaurantResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ReportsRelationManager extends RelationManager
{
    protected static string $relationship = "reports";
    protected static ?string $title = "Reportes";

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("reporter_name")
                    ->label("Reportado por")
                    ->searchable(),
                Tables\Columns\TextColumn::make("report_type")
                    ->label("Tipo")
                    ->badge(),
                Tables\Columns\TextColumn::make("description")
                    ->label("Descripcion")
                    ->limit(50),
                Tables\Columns\TextColumn::make("status")
                    ->label("Estado")
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        "resolved" => "success",
                        "pending" => "warning",
                        "dismissed" => "gray",
                        default => "danger",
                    }),
                Tables\Columns\TextColumn::make("created_at")
                    ->label("Fecha")
                    ->dateTime("d/m/Y")
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make("status")
                    ->options([
                        "pending" => "Pendiente",
                        "resolved" => "Resuelto",
                        "dismissed" => "Descartado",
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make("resolve")
                    ->label("Resolver")
                    ->icon("heroicon-o-check")
                    ->color("success")
                    ->action(fn ($record) => $record->update(["status" => "resolved"]))
                    ->visible(fn ($record) => $record->status === "pending"),
                Tables\Actions\Action::make("dismiss")
                    ->label("Descartar")
                    ->icon("heroicon-o-x-mark")
                    ->color("gray")
                    ->action(fn ($record) => $record->update(["status" => "dismissed"]))
                    ->visible(fn ($record) => $record->status === "pending"),
            ])
            ->defaultSort("created_at", "desc");
    }
}

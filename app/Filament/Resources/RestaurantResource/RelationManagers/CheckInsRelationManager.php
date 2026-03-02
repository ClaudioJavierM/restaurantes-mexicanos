<?php

namespace App\Filament\Resources\RestaurantResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CheckInsRelationManager extends RelationManager
{
    protected static string $relationship = "checkIns";
    protected static ?string $title = "Check-ins";

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("user.name")
                    ->label("Usuario")
                    ->searchable(),
                Tables\Columns\IconColumn::make("verified")
                    ->label("Verificado")
                    ->boolean(),
                Tables\Columns\TextColumn::make("points_earned")
                    ->label("Puntos"),
                Tables\Columns\TextColumn::make("created_at")
                    ->label("Fecha")
                    ->dateTime("d/m/Y H:i")
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make("verified")
                    ->label("Verificado"),
            ])
            ->defaultSort("created_at", "desc");
    }
}

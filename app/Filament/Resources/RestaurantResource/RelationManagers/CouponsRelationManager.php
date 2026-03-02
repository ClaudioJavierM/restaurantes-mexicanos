<?php

namespace App\Filament\Resources\RestaurantResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CouponsRelationManager extends RelationManager
{
    protected static string $relationship = "coupons";
    protected static ?string $title = "Cupones";

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("code")
                    ->label("Codigo")
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make("title")
                    ->label("Titulo"),
                Tables\Columns\TextColumn::make("discount_type")
                    ->label("Tipo"),
                Tables\Columns\TextColumn::make("discount_value")
                    ->label("Valor"),
                Tables\Columns\IconColumn::make("is_active")
                    ->label("Activo")
                    ->boolean(),
                Tables\Columns\TextColumn::make("valid_until")
                    ->label("Valido Hasta")
                    ->date("d/m/Y"),
                Tables\Columns\TextColumn::make("times_used")
                    ->label("Usos"),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make("is_active")
                    ->label("Activo"),
            ])
            ->defaultSort("created_at", "desc");
    }
}

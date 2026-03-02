<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CallResource\Pages;
use App\Models\Call;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class CallResource extends Resource
{
    protected static ?string $model = Call::class;
    protected static ?string $navigationIcon = "heroicon-o-phone";
    protected static ?string $navigationGroup = "Comunicaciones";
    protected static ?string $navigationLabel = "Llamadas IA";
    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("call_started_at")
                    ->label("Fecha")
                    ->dateTime("M d, Y H:i")
                    ->sortable(),
                Tables\Columns\TextColumn::make("caller_phone")
                    ->label("Telefono")
                    ->searchable(),
                Tables\Columns\BadgeColumn::make("category")
                    ->label("Categoria")
                    ->formatStateUsing(fn ($state) => Call::CATEGORIES[$state] ?? $state)
                    ->colors([
                        "success" => "order_inquiry",
                        "info" => "reservation",
                        "warning" => "restaurant_search",
                        "danger" => "owner_support",
                    ]),
                Tables\Columns\TextColumn::make("duration_seconds")
                    ->label("Duracion")
                    ->formatStateUsing(fn ($state) => $state ? gmdate("i:s", $state) : "-"),
                Tables\Columns\BadgeColumn::make("status")->label("Estado"),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make("category")
                    ->label("Categoria")
                    ->options(Call::CATEGORIES),
            ])
            ->defaultSort("call_started_at", "desc");
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make("Detalles")->schema([
                Infolists\Components\TextEntry::make("call_started_at")->label("Fecha")->dateTime(),
                Infolists\Components\TextEntry::make("caller_phone")->label("Telefono"),
                Infolists\Components\TextEntry::make("duration_seconds")
                    ->label("Duracion")
                    ->formatStateUsing(fn ($state) => $state ? gmdate("i:s", $state) : "-"),
                Infolists\Components\TextEntry::make("category")
                    ->label("Categoria")
                    ->formatStateUsing(fn ($state) => Call::CATEGORIES[$state] ?? $state),
            ])->columns(4),
            Infolists\Components\Section::make("Transcripcion")->schema([
                Infolists\Components\TextEntry::make("transcript")->label(""),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListCalls::route("/"),
            "view" => Pages\ViewCall::route("/{record}"),
        ];
    }
}

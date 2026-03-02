<?php

namespace App\Filament\Resources\RestaurantResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Form;

class EventsRelationManager extends RelationManager
{
    protected static string $relationship = "events";
    protected static ?string $title = "Eventos";

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make("title")->required()->label("Titulo"),
            Forms\Components\Textarea::make("description")->label("Descripcion"),
            Forms\Components\Select::make("event_type")
                ->label("Tipo")
                ->options([
                    "live_music" => "Musica en Vivo",
                    "special_dinner" => "Cena Especial",
                    "class" => "Clase de Cocina",
                    "tasting" => "Degustacion",
                    "holiday" => "Evento Festivo",
                    "happy_hour" => "Happy Hour",
                    "other" => "Otro",
                ]),
            Forms\Components\DatePicker::make("event_date")->required()->label("Fecha"),
            Forms\Components\TimePicker::make("start_time")->label("Hora Inicio"),
            Forms\Components\TimePicker::make("end_time")->label("Hora Fin"),
            Forms\Components\TextInput::make("price")->numeric()->label("Precio"),
            Forms\Components\TextInput::make("capacity")->numeric()->label("Capacidad"),
            Forms\Components\Toggle::make("is_active")->label("Activo")->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("title")
                    ->label("Titulo")
                    ->searchable(),
                Tables\Columns\TextColumn::make("event_type")
                    ->label("Tipo")
                    ->badge(),
                Tables\Columns\TextColumn::make("event_date")
                    ->label("Fecha")
                    ->date("d/m/Y")
                    ->sortable(),
                Tables\Columns\TextColumn::make("registered_count")
                    ->label("Registros"),
                Tables\Columns\TextColumn::make("capacity")
                    ->label("Capacidad"),
                Tables\Columns\IconColumn::make("is_active")
                    ->label("Activo")
                    ->boolean(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort("event_date", "desc");
    }
}

<?php
namespace App\Filament\Owner\Resources\EventsResource\Pages;
use App\Filament\Owner\Resources\EventsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditEvent extends EditRecord {
    protected static string $resource = EventsResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}

<?php

namespace App\Filament\Owner\Resources\MyTeamResource\Pages;

use App\Filament\Owner\Resources\MyTeamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyTeam extends ListRecords
{
    protected static string $resource = MyTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Invitar Miembro'),
        ];
    }
}

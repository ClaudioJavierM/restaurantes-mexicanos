<?php

namespace App\Filament\Owner\Resources\MyCouponsResource\Pages;

use App\Filament\Owner\Resources\MyCouponsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyCoupons extends ListRecords
{
    protected static string $resource = MyCouponsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Cupón'),
        ];
    }
}

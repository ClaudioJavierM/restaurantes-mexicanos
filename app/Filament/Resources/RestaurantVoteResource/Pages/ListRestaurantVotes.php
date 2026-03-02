<?php

namespace App\Filament\Resources\RestaurantVoteResource\Pages;

use App\Filament\Resources\RestaurantVoteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRestaurantVotes extends ListRecords
{
    protected static string $resource = RestaurantVoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

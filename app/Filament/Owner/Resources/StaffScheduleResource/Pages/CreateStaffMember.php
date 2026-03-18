<?php

namespace App\Filament\Owner\Resources\StaffScheduleResource\Pages;

use App\Filament\Owner\Resources\StaffScheduleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStaffMember extends CreateRecord
{
    protected static string $resource = StaffScheduleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $restaurant = auth()->user()->allAccessibleRestaurants()->first();
        $data['restaurant_id'] = $restaurant->id;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

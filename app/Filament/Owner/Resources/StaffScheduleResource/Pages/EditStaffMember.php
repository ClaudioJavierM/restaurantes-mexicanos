<?php

namespace App\Filament\Owner\Resources\StaffScheduleResource\Pages;

use App\Filament\Owner\Resources\StaffScheduleResource;
use Filament\Resources\Pages\EditRecord;

class EditStaffMember extends EditRecord
{
    protected static string $resource = StaffScheduleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

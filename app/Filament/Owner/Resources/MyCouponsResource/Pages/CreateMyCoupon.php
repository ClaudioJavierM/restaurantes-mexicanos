<?php

namespace App\Filament\Owner\Resources\MyCouponsResource\Pages;

use App\Filament\Owner\Resources\MyCouponsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMyCoupon extends CreateRecord
{
    protected static string $resource = MyCouponsResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

<?php

namespace App\Filament\Owner\Resources\MenuAbTestsResource\Pages;

use App\Filament\Owner\Resources\MenuAbTestsResource;
use Filament\Resources\Pages\EditRecord;

class EditMenuAbTest extends EditRecord
{
    protected static string $resource = MenuAbTestsResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

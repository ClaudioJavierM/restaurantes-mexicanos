<?php

namespace App\Filament\Owner\Resources\MyQuestionsResource\Pages;

use App\Filament\Owner\Resources\MyQuestionsResource;
use Filament\Resources\Pages\ListRecords;

class ListMyQuestions extends ListRecords
{
    protected static string $resource = MyQuestionsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

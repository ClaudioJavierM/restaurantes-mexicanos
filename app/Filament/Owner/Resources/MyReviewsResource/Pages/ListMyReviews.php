<?php

namespace App\Filament\Owner\Resources\MyReviewsResource\Pages;

use App\Filament\Owner\Resources\MyReviewsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyReviews extends ListRecords
{
    protected static string $resource = MyReviewsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Owner\Widgets\ReviewsStatsWidget::class,
        ];
    }
}

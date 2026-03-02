<?php

namespace App\Filament\Resources\PromotionCouponResource\Pages;

use App\Filament\Resources\PromotionCouponResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPromotionCoupons extends ListRecords
{
    protected static string $resource = PromotionCouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

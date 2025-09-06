<?php

namespace App\Filament\Resources\Discounts\Pages;

use App\Filament\Resources\Discounts\DiscountResource;
use App\Filament\Widgets\DiscountsOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDiscounts extends ListRecords
{
    protected static string $resource = DiscountResource::class;

    protected static ?string $title = "Chegirmalar";

    protected function getHeaderWidgets(): array
    {
        return [
            DiscountsOverview::class
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Chegirma Yaratish"),
        ];
    }
}

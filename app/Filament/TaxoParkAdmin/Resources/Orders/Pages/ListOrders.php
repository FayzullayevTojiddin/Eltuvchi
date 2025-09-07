<?php

namespace App\Filament\TaxoParkAdmin\Resources\Orders\Pages;

use App\Filament\TaxoParkAdmin\Resources\Orders\OrderResource;
use App\Filament\TaxoParkAdmin\Widgets\OrdersOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected static ?string $title = "Buyurtmalar Ro'yxati";

    protected function getHeaderWidgets(): array
    {
        return [
            OrdersOverview::class
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Buyurtma Yaratish"),
        ];
    }
}

<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Widgets\OrdersOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected static ?string $title = "Buyurtmalar";

    protected function getHeaderWidgets(): array
    {
        return [
            OrdersOverview::class
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Yangi Buyurtma Yaratish"),
        ];
    }
}

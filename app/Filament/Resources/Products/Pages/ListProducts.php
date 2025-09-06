<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Widgets\ProductsOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected static ?string $title = "Sovg'alar";

    protected function getHeaderWidgets(): array
    {
        return [
            ProductsOverview::class
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Yangi Sovg'a Qo'shish"),
        ];
    }
}

<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Actions\DisActiveAction;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Widgets\ProductOverview;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected static ?string $title = "Sovg'ani Tahrirlash";

    protected function getHeaderWidgets(): array
    {
        return [
            ProductOverview::class
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label("Sovg'ani O'chirish"),
            ActionGroup::make([
                DisActiveAction::create()
            ])
        ];
    }
}

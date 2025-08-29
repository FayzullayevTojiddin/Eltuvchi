<?php

namespace App\Filament\Resources\TaxoParks\Pages;

use App\Filament\Resources\TaxoParks\TaxoParkResource;
use App\Filament\Widgets\TaxoParkOverview;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTaxoPark extends EditRecord
{
    protected static string $resource = TaxoParkResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            TaxoParkOverview::class
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

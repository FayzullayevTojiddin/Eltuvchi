<?php

namespace App\Filament\Resources\TaxoParks\Pages;

use App\Filament\Actions\DisActiveAction;
use App\Filament\Resources\TaxoParks\TaxoParkResource;
use App\Filament\Widgets\TaxoParkOverview;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTaxoPark extends EditRecord
{
    protected static string $resource = TaxoParkResource::class;

    protected static ?string $title = "TaxoParkni Tahrirlash";

    protected function getHeaderWidgets(): array
    {
        return [
            TaxoParkOverview::class
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label("O'chirish"),
            ActionGroup::make([
                DisActiveAction::create(),
            ])
        ];
    }
}

<?php

namespace App\Filament\TaxoParkAdmin\Resources\Routes\Pages;

use App\Filament\Actions\DisActiveAction;
use App\Filament\TaxoParkAdmin\Resources\Routes\RouteResource;
use App\Filament\TaxoParkAdmin\Widgets\RouteOverview;
use Filament\Resources\Pages\EditRecord;

class EditRoute extends EditRecord
{
    protected static string $resource = RouteResource::class;

    protected static ?string $title = "Yo'lni Tahrirlash";

    protected function getHeaderWidgets(): array
    {
        return [
            RouteOverview::class
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            DisActiveAction::create(),
        ];
    }
}

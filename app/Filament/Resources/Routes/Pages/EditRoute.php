<?php

namespace App\Filament\Resources\Routes\Pages;

use App\Filament\Actions\DisActiveAction;
use App\Filament\Resources\Routes\RouteResource;
use App\Filament\Widgets\RouteOverview;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
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
            DeleteAction::make()->label("Yo'lni O'chirish"),
            ActionGroup::make([
                DisActiveAction::create()
            ])
        ];
    }
}

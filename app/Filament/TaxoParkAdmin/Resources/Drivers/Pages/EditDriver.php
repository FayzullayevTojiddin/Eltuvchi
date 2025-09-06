<?php

namespace App\Filament\TaxoParkAdmin\Resources\Drivers\Pages;

use App\Filament\Actions\ChangeBalanceAction;
use App\Filament\Actions\DisActiveAction;
use App\Filament\Actions\SendMessageAction;
use App\Filament\TaxoParkAdmin\Resources\Drivers\DriverResource;
use App\Filament\TaxoParkAdmin\Widgets\DriverOverview;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDriver extends EditRecord
{
    protected static string $resource = DriverResource::class;

    protected static ?string $title = "Haydovchini Tahrirlash";

    protected function getHeaderWidgets(): array
    {
        return [
            DriverOverview::class
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            DisActiveAction::create(),
            SendMessageAction::create(),
            ChangeBalanceAction::create()
        ];
    }
}

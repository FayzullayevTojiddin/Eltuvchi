<?php

namespace App\Filament\Resources\Regions\Pages;

use App\Filament\Actions\DisActiveAction;
use App\Filament\Resources\Regions\RegionResource;
use App\Livewire\RegionOverview;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRegion extends EditRecord
{
    protected static string $resource = RegionResource::class;

    protected static ?string $title = "Regionni Tahrirlash";

    protected function getHeaderWidgets(): array
    {
        return [
            RegionOverview::class
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label("Regionni o'chirish"),
            ActionGroup::make([
                DisActiveAction::create()
            ])
        ];
    }
}

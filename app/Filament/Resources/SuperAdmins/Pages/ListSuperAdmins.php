<?php

namespace App\Filament\Resources\SuperAdmins\Pages;

use App\Filament\Resources\SuperAdmins\SuperAdminResource;
use App\Filament\Widgets\SuperAdminsOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSuperAdmins extends ListRecords
{
    protected static string $resource = SuperAdminResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            SuperAdminsOverview::class
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Yangi SuperAdmin yaratish"),
        ];
    }
}

<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Filament\Widgets\UsersOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            UsersOverview::class
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Barchasi' => Tab::make(),
            'Mijozlar' => Tab::make()
                ->modifyQueryUsing(fn ($query) => $query->where('role', 'client')),
            'Haydovchilar' => Tab::make()
                ->modifyQueryUsing(fn ($query) => $query->where('role', 'driver')),
            'Taxopark Adminlari' => Tab::make()
                ->modifyQueryUsing(fn ($query) => $query->where('role', 'taxoparkadmin')),
            'SuperAdminlar' => Tab::make()
                ->modifyQueryUsing(fn ($query) => $query->where('role', 'superadmin')),
        ];
    }
}

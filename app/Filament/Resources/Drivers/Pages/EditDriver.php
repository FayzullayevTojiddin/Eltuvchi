<?php

namespace App\Filament\Resources\Drivers\Pages;

use App\Filament\Actions\ChangeBalanceAction;
use App\Filament\Actions\DisActiveAction;
use App\Filament\Actions\SendMessageAction;
use App\Filament\Resources\Drivers\DriverResource;
use App\Filament\Resources\TaxoParks\TaxoParkResource;
use App\Filament\Widgets\DriverOverview;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
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
            DeleteAction::make()->label("O'chirish"),
            ActionGroup::make([
                DisActiveAction::create(),
                SendMessageAction::create(),
                ChangeBalanceAction::create(),
                ViewAction::make()
                    ->button()
                    ->label("TaxoParkga o'tish")
                    ->url(fn ($record) => TaxoParkResource::getUrl('edit', ['record' => $record->taxopark]))
                    ->openUrlInNewTab(),
            ])
        ];
    }
}

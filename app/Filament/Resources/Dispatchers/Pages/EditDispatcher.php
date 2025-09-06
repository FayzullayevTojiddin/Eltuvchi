<?php

namespace App\Filament\Resources\Dispatchers\Pages;

use App\Filament\Actions\DisActiveAction;
use App\Filament\Actions\SendMessageAction;
use App\Filament\Resources\Dispatchers\DispatcherResource;
use App\Filament\Resources\TaxoParks\TaxoParkResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDispatcher extends EditRecord
{
    protected static string $resource = DispatcherResource::class;

    protected static ?string $title = "TaxoPark Adminini tahrirlash";

    protected function getHeaderActions(): array
    {
        return [
            SendMessageAction::create(),
            ActionGroup::make([
                ViewAction::make()->label('Tahrirlash')->button(),
                DisActiveAction::create()->label("Bloklash"),
                DeleteAction::make()->label("O'chirish")->button()->color('black'),
                ViewAction::make()
                    ->button()
                    ->label("TaxoParkga o'tish")
                    ->url(fn ($record) => TaxoParkResource::getUrl('edit', ['record' => $record->taxopark]))
                    ->openUrlInNewTab(),
            ]),
        ];
    }
}

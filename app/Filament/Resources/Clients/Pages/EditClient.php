<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Actions\ChangeBalanceAction;
use App\Filament\Actions\DisActiveAction;
use App\Filament\Actions\SendMessageAction;
use App\Filament\Resources\Clients\ClientResource;
use App\Livewire\ClientOverview;
use App\Traits\TelegramBotTrait;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClient extends EditRecord
{
    use TelegramBotTrait;

    protected static string $resource = ClientResource::class;

    protected static ?string $title = "Mijozni Tahrirlash";

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label("O'chirish"),
            ActionGroup::make([
                DisActiveAction::create(),
                SendMessageAction::create(),
                ChangeBalanceAction::create()
            ])
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ClientOverview::class
        ];
    }
}

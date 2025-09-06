<?php

namespace App\Filament\TaxoParkAdmin\Resources\Orders\Pages;

use App\Filament\TaxoParkAdmin\Resources\Orders\OrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected static ?string $title = "Buyurtmani Tahrirlash";

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}

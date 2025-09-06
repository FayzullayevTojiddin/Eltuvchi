<?php

namespace App\Filament\TaxoParkAdmin\Resources\Orders\Pages;

use App\Filament\TaxoParkAdmin\Resources\Orders\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}

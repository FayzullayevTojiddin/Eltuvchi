<?php

namespace App\Filament\TaxoParkAdmin\Resources\Routes\Pages;

use App\Filament\TaxoParkAdmin\Resources\Routes\RouteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRoute extends CreateRecord
{
    protected static string $resource = RouteResource::class;

    protected static ?string $title = "Yangi Yo'nalish Qo'shish";
}

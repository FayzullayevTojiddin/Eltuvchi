<?php

namespace App\Filament\TaxoParkAdmin\Resources\Drivers\Pages;

use App\Filament\TaxoParkAdmin\Resources\Drivers\DriverResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDriver extends CreateRecord
{
    protected static string $resource = DriverResource::class;

    protected static ?string $title = "Yangi Haydovchi Yaratish";    
}

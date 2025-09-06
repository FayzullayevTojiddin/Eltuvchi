<?php

namespace App\Filament\Resources\TaxoParks\RelationManagers;

use App\Filament\Resources\Drivers\Schemas\DriverForm;
use App\Filament\Resources\Drivers\Tables\DriversTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class DriversRelationManager extends RelationManager
{
    protected static string $relationship = 'drivers';

    protected static ?string $title = "Haydovchilar";

    public function form(Schema $schema): Schema
    {
        return DriverForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return DriversTable::configure($table);
    }
}

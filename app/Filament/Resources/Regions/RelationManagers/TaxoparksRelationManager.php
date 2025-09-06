<?php

namespace App\Filament\Resources\Regions\RelationManagers;

use App\Filament\Resources\TaxoParks\Schemas\TaxoParkForm;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use App\Filament\Resources\TaxoParks\Tables\TaxoParksTable;

class TaxoparksRelationManager extends RelationManager
{
    protected static string $relationship = 'taxoparks';

    public function form(Schema $schema): Schema
    {
        return TaxoParkForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return TaxoParksTable::configure($table);
    }
}

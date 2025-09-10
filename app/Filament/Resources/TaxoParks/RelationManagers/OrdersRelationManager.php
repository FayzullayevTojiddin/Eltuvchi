<?php

namespace App\Filament\Resources\TaxoParks\RelationManagers;

use App\Filament\Resources\Orders\Schemas\OrderForm;
use App\Filament\Resources\Orders\Tables\OrdersTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $title = 'Buyurtmalar';

    public function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }
}
<?php

namespace App\Filament\Resources\TaxoParks\RelationManagers;

use App\Filament\Resources\Routes\Schemas\RouteForm;
use App\Filament\Resources\Routes\Tables\RoutesTable;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoutesFromRelationManager extends RelationManager
{
    protected static string $relationship = 'routesFrom';

    protected static ?string $title = "Boradigan Yo'llar";

    public function form(Schema $schema): Schema
    {
        return RouteForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return RoutesTable::configure($table);
    }
}

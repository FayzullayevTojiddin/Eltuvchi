<?php

namespace App\Filament\Resources\TaxoParks\RelationManagers;

use App\Filament\Resources\Dispatchers\Schemas\DispatcherForm;
use App\Filament\Resources\Dispatchers\Tables\DispatchersTable;
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

class DispatchersRelationManager extends RelationManager
{
    protected static string $relationship = 'dispatchers';

    protected static ?string $title = "TaxoParkAdminlari";

    public function form(Schema $schema): Schema
    {
        return DispatcherForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return DispatchersTable::configure($table);
    }
}

<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Orders\Tables\OrdersTable;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $title = 'Buyurtmalar';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return OrdersTable::configure($table)->recordActions([
            ViewAction::make()
                ->label("Ko'rish")
                ->url(fn ($record) => OrderResource::getUrl('view', ['record' => $record->getKey()]))
                ->button(),
                
            EditAction::make()
                ->label("Tahrirlash")
                ->url(fn ($record) => OrderResource::getUrl('edit', ['record' => $record->getKey()]))
                ->button(),
        ]);
    }
}

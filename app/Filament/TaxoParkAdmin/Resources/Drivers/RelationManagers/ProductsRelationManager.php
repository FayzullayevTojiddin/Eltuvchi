<?php

namespace App\Filament\TaxoParkAdmin\Resources\Drivers\RelationManagers;

use App\Filament\Actions\DriverProductDeliveredAction;
use App\Filament\Resources\Products\Tables\ProductsTable;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $title = "Sovg'alar";

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')
                    ->searchable(),
            TextColumn::make('product.title')
                ->label("Sovg'a Nomi")
                ->searchable(),
            BooleanColumn::make('delivered')
                ->label('Yetqazildimi ?')
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->trueColor('success')
                ->falseColor('danger')
                ->sortable()
        ])
        ->recordActions([
            DriverProductDeliveredAction::create(),
            ActionGroup::make([
                DeleteAction::make()->label("O'chirib Tashlash"),
            ])
        ]);
    }
}

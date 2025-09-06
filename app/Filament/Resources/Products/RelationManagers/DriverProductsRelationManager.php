<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Filament\Actions\DriverProductDeliveredAction;
use App\Filament\Resources\Drivers\DriverResource;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Tables\ProductsTable;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DriverProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'driverProducts';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('driver_id')
                ->relationship('driver', 'details.full_name')
                ->label("Haydovchi")
                ->searchable(['id', 'details.full_name'])
                ->preload(10)
                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->id} - {$record->details["full_name"]}")
                ->columnSpanFull()
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')
                    ->searchable(),
            TextColumn::make('driver.details.full_name')
                ->label("Haydovchi Ismi"),
            TextColumn::make('product.title')
                ->label("Sovg'a Nomi")
                ->searchable(),
            BooleanColumn::make('delivered')
                ->label('Yetqazildimi ?')
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->trueColor('success')
                ->falseColor('danger')
                ->sortable(),
            TextColumn::make('created_at')
                ->label('Berilgan Sana')
                ->dateTime('d-M-Y H:i')
                ->sortable(),
        ])
        ->recordActions([
            DriverProductDeliveredAction::create(),
            ActionGroup::make([
                Action::make('driver')
                    ->label("Haydovchini Ko'rish")
                    ->url(fn ($record) => $record->driver 
                        ? DriverResource::getUrl('edit', ['record' => $record->driver]) 
                        : null, 
                        shouldOpenInNewTab: true
                    )
                    ->button()
                    ->hidden(fn ($record) => blank($record->driver)),
                EditAction::make()->label("Tahrirlash"),
                DeleteAction::make()->label("O'chirib Tashlash"),
            ])
        ])
        ->headerActions([
            CreateAction::make()->label("Sovg'a Berish")
        ]);
    }
}

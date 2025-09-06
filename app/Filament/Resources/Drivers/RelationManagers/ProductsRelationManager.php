<?php

namespace App\Filament\Resources\Drivers\RelationManagers;

use App\Filament\Actions\DriverProductDeliveredAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                Select::make('product_id')
                    ->relationship('product', 'title')
                    ->label("Sov'ga")
                    ->searchable()
                    ->preload(10)
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->id} - {$record->title}")
                    ->columnSpanFull()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
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
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->label("Sovg'a qilish"),
            ])
            ->recordActions([
                DriverProductDeliveredAction::create(),
                ActionGroup::make([
                    EditAction::make()->label("Tahrirlash"),
                    DeleteAction::make()->label("O'chirib Tashlash"),
                ])
            ])
            ->toolbarActions([
                //
            ]);
    }
}

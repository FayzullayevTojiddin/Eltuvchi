<?php

namespace App\Filament\Resources\Discounts\RelationManagers;

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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientsRelationManager extends RelationManager
{
    protected static string $relationship = 'clients';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_id')
                        ->label('Mijoz')
                        ->relationship('client', 'id')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->id . " - " . $record->settings['full_name'])
                        ->searchable()
                        ->columnSpanFull()
                        ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id')
                    ->searchable(),
                TextColumn::make('client.settings.full_name')
                    ->label("Mijozning Nomi"),
                TextColumn::make('discount.title')
                    ->label("Chegirma Nomi"),
                IconColumn::make('used')
                    ->boolean()
                    ->label('Used'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->label("Sovg'a Qilish"),
            ])
            ->recordActions([
                DeleteAction::make()->label("O'chirish"),
            ])
            ->toolbarActions([
                //
            ]);
    }
}

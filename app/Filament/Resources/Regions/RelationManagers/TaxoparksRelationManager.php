<?php

namespace App\Filament\Resources\Regions\RelationManagers;

use App\Filament\Resources\TaxoParks\Schemas\TaxoParkForm;
use App\Filament\Resources\TaxoParks\Tables\TaxoParksTable;
use App\Filament\Resources\TaxoParks\TaxoParkResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use yes;

class TaxoparksRelationManager extends RelationManager
{
    protected static string $relationship = 'taxoparks';

    public function form(Schema $schema): Schema
    {
        return TaxoParkForm::configure($schema);
    }

    // public function infolist(Schema $schema): Schema
    // {
    //     return TaxoPark::configure($schema);
    // }

    public function table(Table $table): Table
    {
        return TaxoParksTable::configure($table)
            ->actions([
                Action::make('view')
                    ->label('Details')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => TaxoParkResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(false)
            ]);
    }
}

<?php

namespace App\Filament\Resources\TaxoParks\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class TaxoParkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Select::make('region_id')
                    ->label('Region')
                    ->relationship('region', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->id . ' - ' . $record->name)
                    ->searchable(['id', 'name'])
                    ->required(),

                TextInput::make('name')
                    ->label('Name')
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('active')
                    ->required(),
            ]);
    }
}
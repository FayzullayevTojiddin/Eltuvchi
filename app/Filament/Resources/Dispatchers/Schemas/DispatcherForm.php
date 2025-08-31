<?php

namespace App\Filament\Resources\Dispatchers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DispatcherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'email')
                    ->searchable(['id', 'email'])
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->id} - {$record->email}")
                    ->required(),
                Select::make('taxopark_id')
                    ->label("TaxoPark")
                    ->relationship('taxopark', 'name')
                    ->searchable(['id', 'name'])
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->id} - {$record->name}")
                    ->preload()
                    ->required(),
                TextInput::make('full_name')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->disabled()
                    ->default('active'),
            ]);
    }
}

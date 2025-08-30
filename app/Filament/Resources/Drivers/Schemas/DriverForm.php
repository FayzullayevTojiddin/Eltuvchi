<?php

namespace App\Filament\Resources\Drivers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class DriverForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Select::make('taxopark_id')
                    ->relationship('taxopark', 'name')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('inactive'),
                TextInput::make('balance')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('points')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('details')
                    ->columnSpanFull(),
                Textarea::make('settings')
                    ->columnSpanFull(),
            ]);
    }
}

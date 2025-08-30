<?php

namespace App\Filament\Resources\Dispatchers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class DispatcherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('taxopark_id')
                    ->required()
                    ->numeric(),
                TextInput::make('full_name')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
                Textarea::make('details')
                    ->columnSpanFull(),
            ]);
    }
}

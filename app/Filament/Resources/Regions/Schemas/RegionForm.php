<?php

namespace App\Filament\Resources\Regions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RegionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id')
                    ->label("ID"),
                
                TextInput::make('name')
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Faol',
                        'inactive' => 'Bloklangan',
                    ])
                    ->disabled()
                    ->default('active'),
            ])->columns(3);
    }
}

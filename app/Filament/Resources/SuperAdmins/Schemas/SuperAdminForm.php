<?php

namespace App\Filament\Resources\SuperAdmins\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SuperAdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('full_name')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
            ]);
    }
}

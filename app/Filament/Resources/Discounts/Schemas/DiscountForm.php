<?php

namespace App\Filament\Resources\Discounts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DiscountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('type')
                    ->disabled()
                    ->label("Turi")
                    ->default('percent'),
                TextInput::make('value')
                    ->label("Qiymati")
                    ->numeric(),
                TextInput::make('points')
                    ->label("Ball Narxi")
                    ->numeric(),
                TextInput::make('title')
                    ->label("Sarlavhasi"),
                TextInput::make('icon')
                    ->label("Icon"),
                TextInput::make('status')
                    ->required()
                    ->default('active')
                    ->disabled(),
            ])->columns(3);
    }
}

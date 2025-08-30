<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // Select::make('route_id')
            //     ->relationship('route', 'name')
            //     ->required(),
            // TextInput::make('passengers')
            //     ->numeric()
            //     ->required(),
            // TextInput::make('phone')
            //     ->tel()
            //     ->required(),
            // TextInput::make('optional_phone')
            //     ->tel(),
            // TextInput::make('note')
            //     ->maxLength(255),
        ]);
    }
}

<?php

namespace App\Filament\Resources\Discounts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DiscountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('icon')
                    ->image()
                    ->directory('products')
                    ->disk('public')
                    ->visibility('public')
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->label('Rasm'),
                TextInput::make('type')
                    ->disabled()
                    ->label("Turi")
                    ->dehydrated(true)
                    ->default('percent'),
                TextInput::make('value')
                    ->label("Qiymati (percent %)")
                    ->numeric(),
                TextInput::make('points')
                    ->label("Ball Narxi")
                    ->numeric(),
                TextInput::make('title')
                    ->label("Sarlavhasi"),
                TextInput::make('status')
                    ->required()
                    ->default('active')
                    ->disabled(),
            ])->columns(3);
    }
}

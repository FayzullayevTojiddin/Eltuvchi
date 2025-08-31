<?php

namespace App\Filament\Resources\Drivers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DriverForm
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
                    ->label('TaxoPark')
                    ->relationship('taxopark', 'name')
                    ->searchable(['id', 'name'])
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->id} - {$record->name}")
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Faol',
                        'inactive' => 'Bloklangan',
                    ])
                    ->disabled()
                    ->default('active'),
                
                Section::make('Hisob')
                    ->label('Balance & Points')
                    ->schema([
                        TextInput::make('balance')
                            ->label('Balans')
                            ->numeric()
                            ->default(0),
                        TextInput::make('points')
                            ->label('Ball')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed(),
                    
                Section::make('Driver Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('details.full_name')
                                    ->label("To'liq Ism")
                                    ->required(),
                                TextInput::make('details.phone_number')
                                    ->label("Telefon Raqami")
                                    ->tel()
                                    ->required(),
                                TextInput::make('details.license_series')
                                    ->label('Litsenziya Seriyasi')
                                    ->required(),
                                TextInput::make('details.license_number')
                                    ->label('Litsenziya Raqami')
                                    ->required(),
                                TextInput::make('details.vehicle_number')
                                    ->label('Mashina Raqami')
                                    ->required(),
                                TextInput::make('details.vehicle_name')
                                    ->label('Mashina Nomi')
                                    ->required(),
                                TextInput::make('details.experience_years')
                                    ->label('Tajribasi')
                                    ->numeric()
                                    ->required(),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed(),

                Section::make('Settings')
                    ->schema([
                        Toggle::make('settings.notifications')
                            ->label('Bildirishnomalar'),
                        Select::make('settings.language')
                            ->label('Til')
                            ->options([
                                'uz' => 'Uzbek',
                                'en' => 'English',
                                'ru' => 'Russian',
                            ])
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed(),
                    
            ])->columns([3]);
    }
}

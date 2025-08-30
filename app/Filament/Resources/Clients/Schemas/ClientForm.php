<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Models\User;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;

class ClientForm
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

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->required()
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
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Section::make('Sozlamalar')
                    ->schema([
                        Toggle::make('settings.notifications')
                            ->label('Bildirishnomalar')
                            ->default(true),

                        Select::make('settings.language')
                            ->label('Til')
                            ->options([
                                'en' => 'English',
                                'uz' => 'O‘zbek',
                                'ru' => 'Русский',
                            ])
                            ->default('en'),

                        TextInput::make('settings.full_name')
                            ->label('To‘liq ism')
                            ->default(fn () => fake()->firstName().' '.fake()->lastName()),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}
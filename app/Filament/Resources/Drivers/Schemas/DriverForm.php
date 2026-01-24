<?php

namespace App\Filament\Resources\Drivers\Schemas;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
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
                    ->searchable(['id', 'email', 'telegram_id'])
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->id} - {$record->email}")
                    ->createOptionForm([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('telegram_id')->required(),
                                TextInput::make('password')->password()->required(),
                                TextInput::make('email')->email()->unique()->required()->columnSpanFull(),
                            ])
                    ])
                    ->createOptionAction(function ($action) {
                        return $action
                            ->modalHeading('Yangi foydalanuvchi yaratish')
                            ->modalButton('Yaratish')
                            ->icon('heroicon-o-plus');
                    })
                    ->suffixAction(
                        Action::make('previewUser')
                            ->label('Ko‘rish')
                            ->icon('heroicon-o-eye')
                            ->modalHeading('Foydalanuvchi maʼlumotlari')
                            ->modalWidth('lg')
                            ->infolist([
                                TextEntry::make('id'),
                                TextEntry::make('email'),
                                TextEntry::make('telegram_id'),
                                TextEntry::make('role')
                            ])
                            ->record(function ($state) {
                                return User::find($state);
                            })
                            ->visible(fn ($state) => filled($state))   
                    )
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

<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Asosiy maʼlumotlar')
                ->schema([
                    Select::make('client_id')
                        ->label('Mijoz')
                        ->relationship('client', 'id')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->id . " - " . $record->settings['full_name'])
                        ->searchable()
                        ->required(),

                    Select::make('driver_id')
                        ->label('Haydovchi')
                        ->relationship('driver', 'name')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->id . " - " . $record->details['full_name'])
                        ->searchable()
                        ->nullable(),

                    Select::make('status')
                        ->label('Holati')
                        ->options([
                            'created'   => 'Yaratilgan',
                            'accepted'  => 'Qabul qilingan',
                            'started'   => 'Boshlangan',
                            'stopped'   => 'To‘xtatilgan',
                            'completed' => 'Tugatildi',
                            'cancelled' => 'Bekor qilingan',
                        ])
                        ->required(),
                ])
                ->columns(3)
                ->collapsible()
                ->columnSpanFull(),

            Section::make('Yo‘nalish va vaqt')
                ->columns(2)
                ->schema([
                    Select::make('route_id')
                        ->relationship('route', 'id')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('passengers')
                        ->numeric()
                        ->minValue(1)
                        ->required(),
                    DatePicker::make('date')
                        ->required(),
                    TimePicker::make('time')
                        ->required(),
                ])
                ->collapsed(),

            Section::make('Moliyaviy ma’lumotlar')
                ->columns(2)
                ->schema([
                    TextInput::make('price_order')
                        ->numeric()
                        ->label('Buyurtma summasi')
                        ->required(),
                    TextInput::make('client_deposit')
                        ->numeric()
                        ->label('Mijoz depositi'),
                    TextInput::make('driver_payment')
                        ->numeric()
                        ->label('Haydovchi to‘lovi'),
                    TextInput::make('discount_percent')
                        ->numeric()
                        ->label('Chegirma (%)'),
                    TextInput::make('discount_summ')
                        ->numeric()
                        ->label('Chegirma summasi'),
                ])
                ->collapsed(),

            Section::make('Aloqa ma’lumotlari')
                ->columns(2)
                ->schema([
                    TextInput::make('phone')
                        ->label('Asosiy telefon')
                        ->tel()
                        ->required(),
                    TextInput::make('optional_phone')
                        ->label('Qo‘shimcha telefon')
                        ->tel(),
                ])
                ->collapsed(),

            Section::make('Qo‘shimcha')
                ->schema([
                    TextInput::make('note')
                        ->label('Izoh')
                        ->maxLength(255),
                ])
                ->collapsed(),

            Section::make('Review')
                ->relationship('review')
                ->schema([
                    Select::make('score')
                        ->options([
                            1 => '1',
                            2 => '2',
                            3 => '3',
                            4 => '4',
                            5 => '5',
                        ])
                        ->required(),
                    Textarea::make('comment'),
                ])
                ->columns(2)
                ->columnSpanFull()
                ->collapsible()
        ]);
    }
}
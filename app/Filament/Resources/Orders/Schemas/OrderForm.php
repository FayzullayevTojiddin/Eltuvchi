<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use App\Models\Route;
use Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
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
                        ->label('Mijoz (ID orqali)')
                        ->relationship('client', 'settings.full_name')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->id . " - " . $record->settings['full_name'])
                        ->searchable(['id'])
                        ->required(),

                    Select::make('driver_id')
                        ->label('Haydovchi (ID orqali)')
                        ->relationship('driver', 'details.full_name')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->id . " - " . $record->details['full_name'])
                        ->searchable(['id'])
                        ->nullable(),
                ])
                ->columns(2)
                ->collapsible()
                ->columnSpanFull(),

            Section::make('Yo‘nalish va vaqt')
                ->columns(2)
                ->schema([
                    Select::make('route_id')
                        ->label("Yo'l (ID orqali)")
                        ->relationship('route', 'name')
                        ->searchable(['id'])
                        ->preload()
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->id . " - " . ($record->name ?? ''))
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $route = Route::find($state);
                                if ($route) {
                                    $set('price_order', $route->price_in);
                                    $set('client_deposit', $route->deposit_client);
                                    $set('driver_payment', $route->fee_per_client);
                                    $set('discount_percent', 0);
                                    $set('discount_summ', 0);
                                }
                            }
                        })
                        ->columnSpanFull(),
                    TextInput::make('passengers')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $routeId = $get('route_id');
                                if ($routeId) {
                                    $route = Route::find($routeId);
                                    if ($route) {
                                        $set('price_order', $route->price_in * $state);
                                        $set('client_deposit', $route->deposit_client * $state);
                                        $set('driver_payment', $route->fee_per_client * $state);
                                        $set('discount_percent', 0);
                                        $set('discount_summ', 0);
                                    }
                                }
                            }),
                    DatePicker::make('date')
                        ->required(),
                    TimePicker::make('time')
                        ->required(),
                ])
                ->collapsible(),

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
                ->disabled()
                ->collapsible(),

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
                    Textarea::make('comment')
                        ->required(),
                    Hidden::make('client_id')
                        ->default(fn ($record, $get) => $record?->order?->client_id ?? null)
                ])
                ->columns(2)
                ->columnSpanFull()
                ->collapsible()
                ->hiddenOn('create')
        ]);
    }
}
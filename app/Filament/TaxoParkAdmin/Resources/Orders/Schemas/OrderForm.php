<?php

namespace App\Filament\TaxoParkAdmin\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use App\Models\Route;
use Auth;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Asosiy maʼlumotlar')
                    ->schema([
                        Select::make('client_id')
                            ->label('Mijoz (ID orqali)')
                            ->relationship('client', 'settings.full_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->id . " - " . $record->settings['full_name'])
                            ->searchable(['id'])
                            ->disabledOn(['edit'])
                            ->required(),

                        Select::make('driver_id')
                            ->label('Haydovchi (ID orqali)')
                            ->relationship(
                                name: 'driver',
                                titleAttribute: 'id',
                                modifyQueryUsing: fn ($query) => $query->where('taxopark_id', Auth::user()->dispatcher->taxopark_id)
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn ($record) => $record->id . " - " . ($record->details['full_name'] ?? '')
                            )
                            ->searchable(['id'])
                            ->preload()
                            ->nullable()
                            ->disabledOn(['edit']),

                        Select::make('status')
                            ->label('Status')
                            ->options(
                                collect(OrderStatus::cases())
                                    ->mapWithKeys(fn ($case) => [$case->value => $case->name])
                                    ->toArray()
                            )
                            ->disabled(),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->columnSpanFull(),

                Section::make('Yo‘nalish va vaqt')
                    ->columns(2)
                    ->schema([
                        Select::make('route_id')
                            ->label("Yo'l")
                            ->relationship(
                                name: 'route',
                                titleAttribute: 'id',
                                modifyQueryUsing: function ($query) {
                                    $user = Auth::user();
                                    $taxoparkId = $user->dispatcher->taxopark_id;
                                    $query->where('taxopark_from_id', $taxoparkId);
                                }
                            )
                            ->disabledOn(['edit'])
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
                            }),
                        Select::make('passengers')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->label("Yo'lovchilarning Soni")
                            ->options([
                                0.25 => 'Pochta',
                                1    => '1 kishi',
                                2   => '2 kishi',
                                3    => '3 kishi',
                                4    => '4 kishi',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $routeId = $get('route_id');

                                if ($routeId) {
                                    $route = Route::find($routeId);

                                    if ($route) {
                                        $multiplier = (float) $state;

                                        $set('price_order',     $route->price_in * $multiplier);
                                        $set('client_deposit',  $route->deposit_client * $multiplier);
                                        $set('driver_payment',  $route->fee_per_client * $multiplier);
                                        $set('discount_percent', 0);
                                        $set('discount_summ', 0);
                                    }
                                }
                            }),
                        DatePicker::make('date')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->required(),
                        TimePicker::make('time')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->required(),
                    ])
                    ->collapsible(),

                Section::make('Moliyaviy ma’lumotlar')
                    ->columns(2)
                    ->schema([
                        TextInput::make('price_order')
                            ->numeric()
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->label('Buyurtma summasi')
                            ->required()
                            ->readOnly(),
                        TextInput::make('client_deposit')
                            ->numeric()
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->label('Mijoz depositi')
                            ->required()
                            ->readOnly(),
                        TextInput::make('driver_payment')
                            ->numeric()
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->label('Haydovchi to‘lovi')
                            ->required()
                            ->readOnly(),
                        TextInput::make('discount_percent')
                            ->numeric()
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->label('Chegirma (%)')
                            ->required()
                            ->readOnly(),
                        TextInput::make('discount_summ')
                            ->numeric()
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->label('Chegirma summasi')
                            ->required()
                            ->readOnly(),
                    ])
                    ->collapsible(),

                Section::make('Aloqa ma’lumotlari')
                    ->columns(2)
                    ->schema([
                        TextInput::make('phone')
                            ->label('Asosiy telefon')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->tel()
                            ->required(),
                        TextInput::make('optional_phone')
                            ->label('Qo‘shimcha telefon')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->tel(),
                    ])
                    ->collapsed(),

                Section::make('Qo‘shimcha')
                    ->schema([
                        TextInput::make('note')
                            ->label('Izoh')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->maxLength(255),
                    ])
                    ->collapsed(),

                Section::make('Review')
                    ->relationship('review')
                    ->schema([
                        Select::make('score')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->options([
                                1 => '1',
                                2 => '2',
                                3 => '3',
                                4 => '4',
                                5 => '5',
                            ]),
                        Textarea::make('comment'),
                        Hidden::make('client_id')
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->default(fn ($record, $get) => $record?->order?->client_id ?? null)
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible()
                    ->hiddenOn(['create', 'edit'])
                ]);
    }
}

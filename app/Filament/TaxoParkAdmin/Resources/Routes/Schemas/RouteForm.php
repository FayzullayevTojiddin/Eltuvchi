<?php

namespace App\Filament\TaxoParkAdmin\Resources\Routes\Schemas;

use Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class RouteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Select::make('taxopark_from_id')
                    ->label('Qayerdan (Taxopark)')
                    ->relationship('fromTaxopark', 'name')
                    ->default(fn () => Auth::user()->dispatcher->taxopark_id)
                    ->disabled()
                    ->dehydrated(true)
                    ->required(),

                Select::make('taxopark_to_id')
                    ->label('Qayerga (Taxopark)')
                    ->relationship('toTaxopark', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->id . " - " . $record->name)
                    ->searchable(['id', 'name'])
                    ->rules([
                        fn (Get $get) =>
                            Rule::unique('routes', 'taxopark_to_id')
                                ->where(fn ($query) => $query->where('taxopark_from_id', $get('taxopark_from_id'))),
                    ])
                    ->required(),

                TextInput::make('status')
                    ->dehydrated(true)
                    ->disabled(),

                TextInput::make('price_in')
                    ->label('Price In (Umumiy)')
                    ->numeric()
                    ->required()
                    ->debounce(1000)
                    ->afterStateUpdated(function (Set $set, $state) {
                        if ($state > 0) {
                            $depositClient = round($state * 0.3);
                            $feePerClient = ceil($state * 0.075);

                            $set('deposit_client', $depositClient);
                            $set('fee_per_client', $feePerClient);
                        } else {
                            $set('deposit_client', null);
                            $set('fee_per_client', null);
                        }
                    }),

                TextInput::make('deposit_client')
                    ->label('Deposit Client (30%)')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(true),

                TextInput::make('distance_km')
                    ->label('Distance (km)')
                    ->numeric()
                    ->required()
                    ->dehydrated(true),

                TextInput::make('fee_per_client')
                    ->label('Fee per Client (7.5%)')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(true),
            ]);
    }
}

<?php

namespace App\Filament\TaxoParkAdmin\Resources\Routes\Schemas;

use Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

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
                    ->dehydrated()
                    ->required(),

                Select::make('taxopark_to_id')
                    ->label('Qayerga (Taxopark)')
                    ->relationship('toTaxopark', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->id . " - " . $record->name)
                    ->searchable(['id', 'name'])
                    ->required(),

                TextInput::make('status')
                    ->disabled(),

                TextInput::make('price_in')
                    ->label('Price In (Umumiy)')
                    ->numeric()
                    ->required()
                    ->debounce(1000)
                    ->afterStateUpdated(function (Set $set, $state) {
                        if ($state > 0) {
                            $depositClient = round($state * 0.3);
                            $feePerClient = round($state * 0.1);

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
                    ->disabled(),

                TextInput::make('distance_km')
                    ->label('Distance (km)')
                    ->numeric()
                    ->required(),

                TextInput::make('fee_per_client')
                    ->label('Fee per Client (10%)')
                    ->numeric()
                    ->disabled(),
            ]);
    }
}

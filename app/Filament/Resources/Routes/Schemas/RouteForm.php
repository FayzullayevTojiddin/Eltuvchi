<?php

namespace App\Filament\Resources\Routes\Schemas;

use App\Enums\RouteStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Validation\Rule;

class RouteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Select::make('taxopark_from_id')
                    ->label('From Taxopark')
                    ->relationship('fromTaxoPark', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->id . ' - ' . $record->name)
                    ->searchable(['id', 'name'])
                    ->required(),

                Select::make('taxopark_to_id')
                    ->label('To Taxopark')
                    ->relationship('toTaxoPark', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->id . ' - ' . $record->name)
                    ->searchable(['id', 'name'])
                    ->rules([
                        fn (Get $get) =>
                            Rule::unique('routes', 'taxopark_to_id')
                                ->where(fn ($query) => $query->where('taxopark_from_id', $get('taxopark_from_id'))),
                    ])
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
                    ->dehydrated(),

                TextInput::make('distance_km')
                    ->label('Distance (km)')
                    ->numeric()
                    ->required(),

                TextInput::make('fee_per_client')
                    ->label('Fee per Client (7.5%)')
                    ->numeric()
                    ->dehydrated(),
            ]);
    }
}

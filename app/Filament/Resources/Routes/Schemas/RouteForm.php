<?php

namespace App\Filament\Resources\Routes\Schemas;

use App\Enums\RouteStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Schemas\Components\Utilities\Set;

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
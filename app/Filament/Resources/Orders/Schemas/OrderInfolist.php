<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('client_id')
                    ->numeric(),
                TextEntry::make('driver_id')
                    ->numeric(),
                TextEntry::make('route_id')
                    ->numeric(),
                TextEntry::make('passengers')
                    ->numeric(),
                TextEntry::make('date')
                    ->date(),
                TextEntry::make('time')
                    ->time(),
                TextEntry::make('price_order')
                    ->numeric(),
                TextEntry::make('client_deposit')
                    ->numeric(),
                TextEntry::make('driver_payment')
                    ->numeric(),
                TextEntry::make('discount_percent')
                    ->numeric(),
                TextEntry::make('discount_summ')
                    ->numeric(),
                TextEntry::make('phone'),
                TextEntry::make('optional_phone'),
                TextEntry::make('status'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Regions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RegionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label('Nomi'),
                TextEntry::make('status')->label('Faol'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->label('Yaratilingan Sana'),
                TextEntry::make('updated_at')
                    ->label('Yangilangan sana')
                    ->dateTime(),
            ]);
    }
}

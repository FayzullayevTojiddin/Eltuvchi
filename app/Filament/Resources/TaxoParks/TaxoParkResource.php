<?php

namespace App\Filament\Resources\TaxoParks;

use App\Filament\Resources\Regions\RelationManagers\TaxoparksRelationManager;
use App\Filament\Resources\TaxoParks\Pages\CreateTaxoPark;
use App\Filament\Resources\TaxoParks\Pages\EditTaxoPark;
use App\Filament\Resources\TaxoParks\Pages\ListTaxoParks;
use App\Filament\Resources\TaxoParks\RelationManagers\DispatchersRelationManager;
use App\Filament\Resources\TaxoParks\RelationManagers\DriversRelationManager;
use App\Filament\Resources\TaxoParks\RelationManagers\OrdersRelationManager;
use App\Filament\Resources\TaxoParks\RelationManagers\RoutesFromRelationManager;
use App\Filament\Resources\TaxoParks\RelationManagers\RoutesToRelationManager;
use App\Filament\Resources\TaxoParks\Schemas\TaxoParkForm;
use App\Filament\Resources\TaxoParks\Tables\TaxoParksTable;
use App\Models\TaxoPark;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;

class TaxoParkResource extends Resource
{
    protected static ?string $model = TaxoPark::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TaxoParkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaxoParksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DriversRelationManager::class,
            DispatchersRelationManager::class,
            RoutesFromRelationManager::class,
            RoutesToRelationManager::class,
            OrdersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTaxoParks::route('/'),
            'create' => CreateTaxoPark::route('/create'),
            'edit' => EditTaxoPark::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\Drivers;

use App\Filament\Resources\Clients\RelationManagers\BalanceHistoriesRelationManager;
use App\Filament\Resources\Clients\RelationManagers\OrdersRelationManager;
use App\Filament\Resources\Clients\RelationManagers\PointHistoriesRelationManager;
use App\Filament\Resources\Drivers\Pages\CreateDriver;
use App\Filament\Resources\Drivers\Pages\EditDriver;
use App\Filament\Resources\Drivers\Pages\ListDrivers;
use App\Filament\Resources\Drivers\Pages\ViewDriver;
use App\Filament\Resources\Drivers\RelationManagers\ProductsRelationManager;
use App\Filament\Resources\Drivers\Schemas\DriverForm;
use App\Filament\Resources\Drivers\Schemas\DriverInfolist;
use App\Filament\Resources\Drivers\Tables\DriversTable;
use App\Models\Driver;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationLabel = "Haydovchilar";

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    public static function form(Schema $schema): Schema
    {
        return DriverForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DriverInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DriversTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            OrdersRelationManager::class,
            BalanceHistoriesRelationManager::class,
            PointHistoriesRelationManager::class,
            ProductsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDrivers::route('/'),
            'create' => CreateDriver::route('/create'),
            'edit' => EditDriver::route('/{record}/edit'),
        ];
    }
}

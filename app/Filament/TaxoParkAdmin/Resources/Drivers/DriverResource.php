<?php

namespace App\Filament\TaxoParkAdmin\Resources\Drivers;

use App\Filament\TaxoParkAdmin\Resources\Drivers\Pages\CreateDriver;
use App\Filament\TaxoParkAdmin\Resources\Drivers\Pages\EditDriver;
use App\Filament\TaxoParkAdmin\Resources\Drivers\Pages\ListDrivers;
use App\Filament\TaxoParkAdmin\Resources\Drivers\RelationManagers\BalanceHistoriesRelationManager;
use App\Filament\TaxoParkAdmin\Resources\Drivers\RelationManagers\OrdersRelationManager;
use App\Filament\TaxoParkAdmin\Resources\Drivers\RelationManagers\PointHistoriesRelationManager;
use App\Filament\TaxoParkAdmin\Resources\Drivers\RelationManagers\ProductsRelationManager;
use App\Filament\TaxoParkAdmin\Resources\Drivers\Schemas\DriverForm;
use App\Filament\TaxoParkAdmin\Resources\Drivers\Tables\DriversTable;
use App\Models\Driver;
use Auth;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationLabel = "Haydovchilar";

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        return parent::getEloquentQuery()->where('taxopark_id', $user->dispatcher->taxopark_id);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['taxopark_id'] = Auth::user()->dispatcher->taxopark_id;
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['taxopark_id'] = Auth::user()->dispatcher->taxopark_id;
        return $data;
    }

    public static function form(Schema $schema): Schema
    {
        return DriverForm::configure($schema);
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

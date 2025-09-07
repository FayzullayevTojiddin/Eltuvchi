<?php

namespace App\Filament\TaxoParkAdmin\Resources\Routes;

use App\Filament\TaxoParkAdmin\Resources\Drivers\RelationManagers\OrdersRelationManager as RelationManagersOrdersRelationManager;
use App\Filament\TaxoParkAdmin\Resources\Routes\Pages\CreateRoute;
use App\Filament\TaxoParkAdmin\Resources\Routes\Pages\EditRoute;
use App\Filament\TaxoParkAdmin\Resources\Routes\Pages\ListRoutes;
use App\Filament\TaxoParkAdmin\Resources\Routes\RelationManagers\OrdersRelationManager;
use App\Filament\TaxoParkAdmin\Resources\Routes\Schemas\RouteForm;
use App\Filament\TaxoParkAdmin\Resources\Routes\Tables\RoutesTable;
use App\Models\Route;
use Auth;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RouteResource extends Resource
{
    protected static ?string $model = Route::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;
    
    protected static ?string $navigationLabel = "Yo'llar";

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $taxoparkId = $user->dispatcher->taxopark_id;

        return parent::getEloquentQuery()
            ->where(function ($q) use ($taxoparkId) {
                $q->where('taxopark_from_id', $taxoparkId)
                ->orWhere('taxopark_to_id', $taxoparkId);
            });
    }

    public static function form(Schema $schema): Schema
    {
        return RouteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoutesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagersOrdersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoutes::route('/'),
            'create' => CreateRoute::route('/create'),
            'edit' => EditRoute::route('/{record}/edit'),
        ];
    }
}

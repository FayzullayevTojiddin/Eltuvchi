<?php

namespace App\Filament\TaxoParkAdmin\Resources\Orders;

use App\Filament\TaxoParkAdmin\Resources\Orders\Pages\CreateOrder;
use App\Filament\TaxoParkAdmin\Resources\Orders\Pages\EditOrder;
use App\Filament\TaxoParkAdmin\Resources\Orders\Pages\ListOrders;
use App\Filament\TaxoParkAdmin\Resources\Orders\RelationManagers\HistoriesRelationManager;
use App\Filament\TaxoParkAdmin\Resources\Orders\Schemas\OrderForm;
use App\Filament\TaxoParkAdmin\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;
use Auth;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static ?string $navigationLabel = "Buyurtmalar";

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $taxoparkId = $user->dispatcher->taxopark_id;

        return parent::getEloquentQuery()
            ->whereHas('route', function ($q) use ($taxoparkId) {
                $q->where('taxopark_from_id', $taxoparkId)
                ->orWhere('taxopark_to_id', $taxoparkId);
            });
    }

    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            HistoriesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}

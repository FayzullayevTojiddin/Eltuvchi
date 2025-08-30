<?php

namespace App\Filament\Resources\Dispatchers;

use App\Filament\Resources\Dispatchers\Pages\CreateDispatcher;
use App\Filament\Resources\Dispatchers\Pages\EditDispatcher;
use App\Filament\Resources\Dispatchers\Pages\ListDispatchers;
use App\Filament\Resources\Dispatchers\Pages\ViewDispatcher;
use App\Filament\Resources\Dispatchers\Schemas\DispatcherForm;
use App\Filament\Resources\Dispatchers\Schemas\DispatcherInfolist;
use App\Filament\Resources\Dispatchers\Tables\DispatchersTable;
use App\Models\Dispatcher;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DispatcherResource extends Resource
{
    protected static ?string $model = Dispatcher::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return DispatcherForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DispatcherInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DispatchersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDispatchers::route('/'),
            'create' => CreateDispatcher::route('/create'),
            'view' => ViewDispatcher::route('/{record}'),
            'edit' => EditDispatcher::route('/{record}/edit'),
        ];
    }
}

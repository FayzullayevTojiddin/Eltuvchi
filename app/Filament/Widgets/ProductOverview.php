<?php

namespace App\Filament\Widgets;

use App\Models\DriverProduct;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductOverview extends StatsOverviewWidget
{
    public ?Product $record = null;
    protected function getStats(): array
    {
        $total = DriverProduct::where('product_id', $this->record->id)->count();
        $used = DriverProduct::where('product_id', $this->record->id)->where('delivered', true)->count();
        $unused = DriverProduct::where('product_id', $this->record->id)->where('delivered', false)->count();

        return [
            Stat::make('Jami berilgan Mahsulotlar', $total)
                ->description('Barcha driver-product yozuvlari')
                ->color('primary'),

            Stat::make('Topshirilgan Mahsulotlar', $used)
                ->description('Yetqazilganlari')
                ->color('success'),

            Stat::make('Topshirilmagan Mahsulotlar', $unused)
                ->description('Yetqazilmaganlari')
                ->color('danger'),
        ];
    }
}

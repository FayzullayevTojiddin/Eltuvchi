<?php

namespace App\Filament\Widgets;

use App\Models\ClientDiscount;
use App\Models\Discount;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DiscountOverview extends StatsOverviewWidget
{
    public ?Discount $record = null;

    protected function getStats(): array
    {
        $total = ClientDiscount::where('discount_id', $this->record->id)->count();
        $used = ClientDiscount::where('discount_id', $this->record->id)->where('used', true)->count();
        $unused = ClientDiscount::where('discount_id', $this->record->id)->where('used', false)->count();

        return [
            Stat::make('Jami berilgan chegirmalar', $total)
                ->description('Barcha client-discount yozuvlari')
                ->color('primary'),

            Stat::make('Ishlatilgan chegirmalar', $used)
                ->description('Foydalanilganlar')
                ->color('success'),

            Stat::make('Ishlatilmagan chegirmalar', $unused)
                ->description('Foydalanilmaganlar')
                ->color('danger'),
        ];
    }
}

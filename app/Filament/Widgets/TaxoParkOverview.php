<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\TaxoPark;
use DB;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TaxoParkOverview extends StatsOverviewWidget
{
    public ?TaxoPark $record = null;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $taxopark = $this->record;
        $taxopark->loadCount([
            'routesFrom',
            'routesTo',
            'drivers',
            'dispatchers',
        ]);
        $taxopark->orders_count = DB::table('orders')
            ->join('routes', 'orders.route_id', '=', 'routes.id')
            ->where(function ($q) use ($taxopark) {
                $q->where('routes.taxopark_from_id', $taxopark->id)
                ->orWhere('routes.taxopark_to_id', $taxopark->id);
            })
            ->count();
        $taxopark->active_orders_count = DB::table('orders')
            ->join('routes', 'orders.route_id', '=', 'routes.id')
            ->where(function ($q) use ($taxopark) {
                $q->where('routes.taxopark_from_id', $taxopark->id)
                ->orWhere('routes.taxopark_to_id', $taxopark->id);
            })
            ->where('orders.status', 'active')
            ->count();
        $taxopark->completed_orders_count = DB::table('orders')
            ->join('routes', 'orders.route_id', '=', 'routes.id')
            ->where(function ($q) use ($taxopark) {
                $q->where('routes.taxopark_from_id', $taxopark->id)
                ->orWhere('routes.taxopark_to_id', $taxopark->id);
            })
            ->where('orders.status', 'completed')
            ->count();

        return [
            Stat::make('Jami Buyurtmalar', $taxopark->orders_count)
                ->description('Ushbu taxopark buyurtmalari')
                ->descriptionIcon('heroicon-o-clipboard-document')
                ->color('primary'),

            Stat::make('Faol Buyurtmalar', $taxopark->active_orders_count)
                ->description('Hozir davom etayotganlar')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Yakunlangan Buyurtmalar', $taxopark->completed_orders_count)
                ->description('Muvaffaqiyatli tugaganlar')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Haydovchilar', $taxopark->drivers_count)
                ->description('Ushbu taxopark haydovchilari')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('info'),

            Stat::make('Dispetcherlar', $taxopark->dispatchers_count)
                ->description('Ushbu taxopark dispetcherlari')
                ->descriptionIcon('heroicon-o-phone')
                ->color('secondary'),

            Stat::make('Boradigan joylar', $taxopark->routes_from_count)
                ->description('Qancha boshqa joyga boradi')
                ->descriptionIcon('heroicon-o-arrow-up-right')
                ->color('info'),

            Stat::make('Keladigan joylar', $taxopark->routes_to_count)
                ->description('Qancha joydan keladi')
                ->descriptionIcon('heroicon-o-arrow-down-left')
                ->color('info'),
        ];
    }

    protected function getColumns(): int|array|null
    {
        return 3;
    }
}
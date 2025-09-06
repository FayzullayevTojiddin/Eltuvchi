<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrdersReview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $completed = Order::where('status', OrderStatus::Completed)->count();
        $cancelled = Order::where('status', OrderStatus::Cancelled)->count();
        $created   = Order::where('status', OrderStatus::Created)->count();
        $accepted  = Order::where('status', OrderStatus::Accepted)->count();
        $started   = Order::where('status', OrderStatus::Started)->count();
        $stopped   = Order::where('status', OrderStatus::Stopped)->count();

        $all = $completed + $cancelled + $created + $accepted + $started + $stopped;

        return [
            Stat::make('Barcha buyurtmalar', $all)
                ->description('Umumiy soni')
                ->color('primary'),

            Stat::make('Yakunlangan', $completed)
                ->description('Muvaffaqiyatli yakunlanganlar')
                ->color('success'),

            Stat::make('Bekor qilingan', $cancelled)
                ->description('Mijoz yoki tizim tomonidan')
                ->color('danger'),

            Stat::make('Yaratilgan', $created)
                ->description('Yangi buyurtmalar')
                ->color('info'),

            Stat::make('Qabul qilingan', $accepted)
                ->description('Driver tomonidan qabul qilingan')
                ->color('warning'),

            Stat::make('Boshlangan', $started)
                ->description('Yo‘lda bo‘lgan buyurtmalar')
                ->color('secondary'),

            Stat::make('To‘xtatilgan', $stopped)
                ->description('Jarayonda to‘xtatilganlar')
                ->color('gray'),
        ];
    }
}
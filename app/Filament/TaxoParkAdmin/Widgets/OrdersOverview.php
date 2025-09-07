<?php

namespace App\Filament\TaxoParkAdmin\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrdersOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $taxoparkId = Auth::user()->dispatcher->taxopark_id;

        $query = Order::query()->whereHas('route', function ($q) use ($taxoparkId) {
            $q->where('taxopark_from_id', $taxoparkId)
              ->orWhere('taxopark_to_id', $taxoparkId);
        });

        $completed = (clone $query)->where('status', OrderStatus::Completed)->count();
        $cancelled = (clone $query)->where('status', OrderStatus::Cancelled)->count();
        $created   = (clone $query)->where('status', OrderStatus::Created)->count();
        $accepted  = (clone $query)->where('status', OrderStatus::Accepted)->count();
        $started   = (clone $query)->where('status', OrderStatus::Started)->count();
        $stopped   = (clone $query)->where('status', OrderStatus::Stopped)->count();

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
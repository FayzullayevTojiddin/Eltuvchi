<?php

namespace App\Filament\Widgets;

use App\Models\Driver;
use App\Models\Order;
use App\Enums\OrderStatus;
use App\Models\BalanceHistory;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DriverOverview extends StatsOverviewWidget
{
    public ?Driver $record = null;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $allOrders = Order::where('driver_id', $this->record->id)->count();

        $cancelledOrders = Order::where('driver_id', $this->record->id)
            ->where('status', OrderStatus::Cancelled)
            ->count();

        $completedOrders = Order::where('driver_id', $this->record->id)
            ->where('status', OrderStatus::Completed)
            ->count();

        $inProgressOrders = $allOrders - ($cancelledOrders + $completedOrders);

        $totalEarned = Order::where('driver_id', $this->record->id)
            ->where('status', OrderStatus::Completed)
            ->sum('price_order');

        $totalDriverDeposit = Order::where('driver_id', $this->record->id)
            ->where('status', OrderStatus::Completed)
            ->sum('driver_payment');

        $netIncome = $totalEarned - $totalDriverDeposit;

        $totalMinus = BalanceHistory::where('balanceable_id', $this->record->id)
            ->where('balanceable_type', Driver::class)
            ->where('type', 'minus')
            ->sum('amount');

        $totalPlus = BalanceHistory::where('balanceable_id', $this->record->id)
            ->where('balanceable_type', Driver::class)
            ->where('type', 'plus')
            ->sum('amount');

        return [
            Stat::make('Barcha buyurtmalar', $allOrders),
            Stat::make('Bekor qilinganlar', $cancelledOrders),
            Stat::make('Yakunlanganlar', $completedOrders),
            Stat::make('Jarayondagilar', $inProgressOrders),
            Stat::make('Haydovchi sof daromadi', number_format($netIncome, 0, '.', ' ') . ' so‘m'),
            Stat::make('Umumiy Foydalangan Summasi', number_format($totalMinus, 0, '.', ' ') . ' so‘m'),
            Stat::make('Umumiy Tushurilgan Summasi', number_format($totalPlus, 0, '.', ' ') . ' so‘m')
        ];
    }
}
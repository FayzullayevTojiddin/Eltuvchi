<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Order;
use App\Models\BalanceHistory;
use App\Enums\OrderStatus;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientOverview extends StatsOverviewWidget
{
    public ?Client $record = null;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $allOrders = Order::where('client_id', $this->record->id)->count();
        $cancelledOrders = Order::where('client_id', $this->record->id)
            ->where('status', OrderStatus::Cancelled)
            ->count();
        $completedOrders = Order::where('client_id', $this->record->id)
            ->where('status', OrderStatus::Completed)
            ->count();

        $inProgressOrders = $allOrders - ($cancelledOrders + $completedOrders);

        $totalMinus = BalanceHistory::where('balanceable_id', $this->record->id)
            ->where('balanceable_type', Client::class)
            ->where('type', 'minus')
            ->sum('amount');

        $totalPlus = BalanceHistory::where('balanceable_id', $this->record->id)
            ->where('balanceable_type', Client::class)
            ->where('type', 'plus')
            ->sum('amount');

        return [
            Stat::make('Barcha buyurtmalar', $allOrders),
            Stat::make('Bekor qilinganlar', $cancelledOrders),
            Stat::make('Yakunlanganlar', $completedOrders),
            Stat::make('Jarayondagilar', $inProgressOrders),
            Stat::make('Umumiy Foydalangan Summasi', number_format($totalMinus, 0, '.', ' ') . ' so‘m'),
            Stat::make('Umumiy Tushurilgan Summasi', number_format($totalPlus, 0, '.', ' ') . ' so‘m')
        ];
    }
}
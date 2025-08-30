<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Order;
use App\Models\BalanceHistory;
use App\Enums\OrderStatus; // agar enum ishlatsangiz
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

        $balanceHistoryCount = BalanceHistory::where('client_id', $this->record->id)->count();
        $balanceTotal = BalanceHistory::where('client_id', $this->record->id)->sum('amount');

        return [
            Stat::make('Barcha buyurtmalari', $allOrders),
            Stat::make('Bekor qilinganlari', $cancelledOrders),
            Stat::make('Yakunlanganlari', $completedOrders),
            Stat::make('Jarayondagilari', $inProgressOrders),
            Stat::make('Balans tarixlari soni', $balanceHistoryCount),
            Stat::make('Umumiy balans harakati', number_format($balanceTotal, 0, '.', ' ')),
        ];
    }
}
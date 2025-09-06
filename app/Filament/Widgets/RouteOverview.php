<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Route;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RouteOverview extends StatsOverviewWidget
{
    public ?Route $record = null;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $route = $this->record;

        $all = $route->orders()->count();
        $completed = $route->orders()->where('status', OrderStatus::Completed)->count();
        $cancelled = $route->orders()->where('status', OrderStatus::Cancelled)->count();
        $processing = $all - ($completed + $cancelled);
        return [
            Stat::make('Barcha Orderlar', $all),
            Stat::make('Yakunlangan', $completed),
            Stat::make('Bekor qilingan', $cancelled),
            Stat::make('Jarayonda', $processing),
        ];
    }
}

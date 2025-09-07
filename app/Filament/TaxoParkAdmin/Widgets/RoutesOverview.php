<?php

namespace App\Filament\TaxoParkAdmin\Widgets;

use App\Models\Route;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class RoutesOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $taxoparkId = Auth::user()->dispatcher->taxopark_id;

        $total = Route::where('taxopark_from_id', $taxoparkId)
            ->orWhere('taxopark_to_id', $taxoparkId)
            ->count();

        $active = Route::where(function ($q) use ($taxoparkId) {
                $q->where('taxopark_from_id', $taxoparkId)
                  ->orWhere('taxopark_to_id', $taxoparkId);
            })
            ->where('status', 'active')
            ->count();

        $inactive = $total - $active;

        return [
            Stat::make('Barcha yo‘nalishlar', $total)
                ->description('Umumiy soni')
                ->color('primary'),

            Stat::make('Faol yo‘nalishlar', $active)
                ->description('Hozirda faol')
                ->color('success'),

            Stat::make('Nofaol yo‘nalishlar', $inactive)
                ->description('O‘chirilgan / yopilgan')
                ->color('danger'),
        ];
    }
}
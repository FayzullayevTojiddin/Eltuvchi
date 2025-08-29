<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Dispatcher;
use App\Models\Driver;
use App\Models\SuperAdmin;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UsersOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Userlar', User::count())
                ->description('Umumiy foydalanuvchilar')
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Haydovchilar', Driver::count())
                ->description('Faol haydovchilar')
                ->icon('heroicon-o-truck')
                ->color('info'),

            Stat::make('Mijozlar', Client::count())
                ->description('Xizmatdan foydalanuvchilar')
                ->icon('heroicon-o-user-circle')
                ->color('success'),

            Stat::make('TaxoPark Adminlari', Dispatcher::count())
                ->description('Park boshqaruvchilari')
                ->icon('heroicon-o-briefcase')
                ->color('warning'),

            Stat::make('SuperAdminlar', SuperAdmin::count())
                ->description('Tizim boshqaruvchilari')
                ->icon('heroicon-o-shield-check')
                ->color('danger'),
        ];
    }

    protected function getColumns(): int|array|null
    {
        return 5;
    }
}
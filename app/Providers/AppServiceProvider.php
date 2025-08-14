<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Order;
use App\Observers\ClientObserve;
use App\Observers\OrderObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Order::observe(OrderObserver::class);
        Client::observe(ClientObserve::class); // For testing
    }
}

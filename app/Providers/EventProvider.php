<?php

namespace App\Providers;

use App\Events\OrderCreated;
use App\Events\OrderUpdated;
use App\Listeners\SendOrderCreated;
use App\Listeners\SendOrderUpdated;
use Illuminate\Support\ServiceProvider;

class EventProvider extends ServiceProvider
{
    
    protected $listen = [
        OrderCreated::class => [
            SendOrderCreated::class,
        ],
        OrderUpdated::class => [
            SendOrderUpdated::class,
        ],
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        
    }
}

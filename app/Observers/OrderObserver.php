<?php

namespace App\Observers;

use App\Events\OrderCreated;
use App\Events\OrderUpdated;
use App\Models\Order;

class OrderObserver
{
    public function created(Order $order): void
    {
        OrderCreated::dispatch($order);
    }

    public function updated(Order $order): void
    {
        OrderUpdated::dispatch($order);
    }
}
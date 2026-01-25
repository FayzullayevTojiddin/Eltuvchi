<?php

namespace App\Observers;

use App\Enums\OrderStatus;
use App\Events\OrderCreated;
use App\Events\OrderUpdated;
use App\Models\Order;
use Auth;

class OrderObserver
{
    public function created(Order $order): void
    {
        $user = Auth::user();

        OrderCreated::dispatch($order);
    }

    public function updated(Order $order): void
    {
        $user = Auth::user();

        OrderUpdated::dispatch($order);
    }
}
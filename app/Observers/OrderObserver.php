<?php

namespace App\Observers;

use App\Enums\OrderStatus;
use App\Models\Order;
use Auth;

class OrderObserver
{
    public function created(Order $order): void
    {
        $user = Auth::user();
        
        $order->histories()->create([
            'status' => OrderStatus::Created,
            'changed_by_id' => $user->id,
            'changed_by_type' => get_class($user),
            'description' => 'Buyurtma yaratildi',
        ]);
    }

    public function updated(Order $order): void
    {
        $user = Auth::user();
        
        $description = $order->temp_description ?? 'Buyurtma yangilandi';

        $order->histories()->create([
            'status' => $order->status,
            'changed_by_id' => $user->id,
            'changed_by_type' => get_class($user),
            'description' => $description,
        ]);
    }
}
<?php

namespace App\Observers;

use App\Models\BalanceHistory;
use App\Models\Client;
use App\Models\ClientDiscount;
use App\Models\Order;
use App\Models\PointHistory;
use App\Models\Referral;

class ClientObserve
{
    public function created(Client $client)
    {
        $client->balance = rand(10000, 750000);
        $client->points = rand(75, 1000);
        $client->save();

        Order::factory()->count(rand(3, 7))->create([
            'client_id' => $client->id
        ]);

        ClientDiscount::factory()->count(rand(2, 5))->create([
            'client_id' => $client->id
        ]);

        Referral::factory()->count(rand(1, 3))->create([
            'referred_by' => $client->user->id,
        ]);

        BalanceHistory::factory()->count(rand(1, 4))->create([
            'balanceable_type' => get_class($client),
            'balanceable_id' => $client->id
        ]);

        PointHistory::factory()->count(rand(1, 5))->create([
            'pointable_type' => get_class($client),
            'pointable_id' => $client->id
        ]);
    }
}

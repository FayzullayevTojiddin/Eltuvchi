<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('client.{clientId}.orders', function ($user, $clientId) {
    return $user->client && $user->client->id === (int) $clientId;
});

Broadcast::channel('driver.{driverId}.orders', function ($user, $driverId) {
    return $user->driver && $user->driver->id === (int) $driverId;
});

Broadcast::channel('taxopark.{taxoparkId}.orders', function ($user, $taxoparkId) {
    return $user->driver && $user->driver->taxopark_id === (int) $taxoparkId;
});

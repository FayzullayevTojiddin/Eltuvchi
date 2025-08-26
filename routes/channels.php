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
    // return $user->client->id == (int) $clientId;
    return true;
});

Broadcast::channel('driver.{driverId}.orders', function ($user, $driverId) {
    // return $user->driver->id == (int) $driverId;
    return true;
});

Broadcast::channel('taxopark.{taxoparkId}.orders', function ($user, $taxoparkId) {
    // return $user->driver->taxopark_id == (int) $taxoparkId;
    return true;
});

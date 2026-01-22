<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverProfileController extends Controller
{
    public function me(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->client) {
            return $this->error([], 404, 'Driver not found.');
        }

        return $this->success([
            'id'         => $user->id,
            'driver_id' => $user->driver->id,
            'role'       => $user->role,
            'telegram_id'=> $user->telegram_id,
            'balance' => $user->driver->balance,
        ], 200, 'Driver profile retrieved successfully.');
    }
}

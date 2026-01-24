<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DriverMeController extends Controller
{
    public function __invoke(Request $request)
    {
        $driver = $request->user()?->driver;
        return $this->response([
            'balance' => $driver->balance
        ]);
    }
}

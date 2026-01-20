<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function dashboard()
    {
        $client = Auth::user()->client;

        $data = [
            'orders_count' => $client->orders()->count(),
            'balance'      => $client->balance,
            'points'       => $client->points,
        ];

        return $this->response($data);
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Resources\BalanceHistoryResource;
use App\Models\Client;
use Auth;
use Illuminate\Http\Request;

class BalanceHistoryController extends Controller
{
    public function client_balance_history(Request $request)
    {
        $client = Auth::user()->client;
        $histories = $client->balanceHistories()->orderBy('created_at', 'desc')->get();
        return $this->response(BalanceHistoryResource::collection($histories));
    }

    public function driver_balance_history(Request $request)
    {
        $driver = Auth::user()->driver;
        $histories = $driver->balanceHistories()->orderBy('created_at', 'desc')->get();
        return $this->response(BalanceHistoryResource::collection($histories));
    }
}
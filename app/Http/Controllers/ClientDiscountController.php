<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientDiscountResource;
use App\Http\Resources\DiscountResource;
use App\Models\ClientDiscount;
use Illuminate\Support\Facades\Auth;

class ClientDiscountController extends Controller
{
    public function index()
    {
        $client = Auth::user()->client; 
        $discounts = ClientDiscount::with('discount')
            ->where('client_id', $client->id)
            ->where('used', false)
            ->get();

        return $this->response(ClientDiscountResource::collection($discounts));
    }
}
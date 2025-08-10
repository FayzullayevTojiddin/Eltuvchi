<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientOrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientOrderController extends Controller
{
    public function index(Request $request)
    {
        $client = Auth::user()->client;

        $query = Order::where('client_id', $client->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->get();

        return response()->json([
            'success' => true,
            'data' => ClientOrderResource::collection($orders)
        ]);
    }

    public function show($order_id)
    {
        $order = Order::with(['client', 'driver', 'route', 'histories'])->find($order_id);

        if (!$order) {
            return $this->error([], 404, 'Order not found.');
        }

        return $this->success(new ClientOrderResource($order), 200, 'Order retrieved successfully.');
    }
}
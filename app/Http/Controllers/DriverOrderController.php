<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientOrderResource;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class DriverOrderController extends Controller
{
    public function index()
    {
        $driver = Auth::user()->driver;
        $orders = Order::query()
            ->whereHas('route', function ($q) use ($driver) {
                $q->where('taxopark_from_id', $driver->taxopark_id)
                ->orWhere('taxopark_to_id', $driver->taxopark_id);
            })
            ->with(['driver', 'route'])
            ->latest()
            ->get();

        return $this->response(ClientOrderResource::collection($orders));
    }

    public function my_orders()
    {
        $driver = Auth::user()->driver;
        $query = Order::query()
            ->whereHas('route', function ($q) use ($driver) {
                $q->where('taxopark_from_id', $driver->taxopark_id)
                ->orWhere('taxopark_to_id', $driver->taxopark_id);
            })
            ->with(['client', 'route', 'review', 'histories'])
            ->latest();
        if ($status = request('status')) {
            $query->where('status', $status);
        }
        $orders = $query->get();
        return $this->response(ClientOrderResource::collection($orders));
    }
}
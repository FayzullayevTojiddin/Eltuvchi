<?php

namespace App\Http\Controllers;

use App\Enums\RouteStatus;
use App\Http\Resources\RouteResource;
use App\Models\Route;
use Illuminate\Support\Facades\Cache;

class RouteController extends Controller
{
    public function check(int $from, int $to)
    {
        $cacheKey = "route_{$from}_{$to}";
        $route = Cache::remember($cacheKey, now()->addHour(), function () use ($from, $to) {
            return Route::with(['fromTaxopark', 'toTaxopark'])
                ->where('taxopark_from_id', $from)
                ->where('taxopark_to_id', $to)
                ->where('status', RouteStatus::ACTIVE->value)
                ->first();
        });
        if (!$route) {
            return $this->error([], 404, 'Route not found.');
        }
        return $this->response(new RouteResource($route));
    }
}
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BalanceHistoryController;
use App\Http\Controllers\ClientCancelOrderController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientDiscountController;
use App\Http\Controllers\ClientOrderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderReviewController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\RouteController;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;


Route::post('/auth', [AuthController::class, 'telegramAuth']);

Route::get('/regions', [RegionController::class, 'index']);
Route::get('/regions/{region_id}', [RegionController::class, 'show']);
Route::get('/routes/check/{from}/{to}', [RouteController::class, 'check']);

Route::prefix('/client')->middleware('auth:sanctum')->group(function (){
    Route::get('/dashboard', [ClientController::class, 'dashboard']);
    Route::apiResource('/my_discounts', ClientDiscountController::class);
    Route::post('orders/{order}/review', [OrderReviewController::class, 'client_review'])->name('orders.client_review');
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [ClientOrderController::class, 'index']);
    Route::get('/orders/{order}', [ClientOrderController::class, 'show']);
    Route::delete('/orders/{order}', [ClientCancelOrderController::class, 'cancel']);
    Route::get('/histories', [BalanceHistoryController::class, 'client_balance_history']);
});

Route::prefix('/driver')->middleware('auth:sanctum')->group(function() {
    Route::get('/histories', [BalanceHistoryController::class, 'driver_balance_history']);
});
Route::prefix('/super-admin')->group(function() {});
Route::prefix('/dispatcher')->group(function() {});
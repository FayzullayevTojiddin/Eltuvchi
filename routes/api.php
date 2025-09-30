<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BalanceHistoryController;
use App\Http\Controllers\ClientCancelOrderController;
use App\Http\Controllers\ClientCompletedOrderController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientDiscountController;
use App\Http\Controllers\ClientMarketController;
use App\Http\Controllers\ClientOrderController;
use App\Http\Controllers\ClientProfileController;
use App\Http\Controllers\DriverCancelOrderController;
use App\Http\Controllers\DriverStoppedOrderController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\DriverGetOrderController;
use App\Http\Controllers\DriverMarketController;
use App\Http\Controllers\DriverOrderController;
use App\Http\Controllers\DriverStartOrderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderReviewController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::post('/auth', [AuthController::class, 'telegramAuth']);
Route::get('/test', [TestController::class, 'index'])->middleware('auth:sanctum');
Route::get('/regions', [RegionController::class, 'index']);
Route::get('/regions/{region_id}', [RegionController::class, 'show']);
Route::get('/routes/check/{from}/{to}', [RouteController::class, 'check']);
Route::get('/referrals', [ReferralController::class, 'index'])->middleware('auth:sanctum');
Route::post('/referrals', [ReferralController::class, 'referred'])->middleware('auth:sanctum');
Route::get('/balance-history', [BalanceHistoryController::class, 'balance_history'])->middleware('auth:sanctum');

Route::prefix('/client')->middleware(['auth:sanctum', 'role_status:client'])->group(function (){
    Route::get('/dashboard', [ClientController::class, 'dashboard']);
    Route::apiResource('/my_discounts', ClientDiscountController::class);
    Route::post('orders/{order}/review', [OrderReviewController::class, 'client_review'])->name('orders.client_review');
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [ClientOrderController::class, 'index']);
    Route::get('/orders/{order}', [ClientOrderController::class, 'show']);
    Route::delete('/orders/{order}', [ClientCancelOrderController::class, 'cancel']);
    Route::post('/orders/{order}/complete', [ClientCompletedOrderController::class, 'complete_order']);
    Route::get('/me', [ClientProfileController::class, 'me']);
    Route::put('/me', [ClientProfileController::class, 'edit']);
    Route::get('/market', [ClientMarketController::class, 'index']);
    Route::post('/market', [ClientMarketController::class, 'store']);
});

Route::prefix('/driver')->middleware(['auth:sanctum', 'role_status:driver'])->group(function() {
    Route::get('/dashboard', [DriverController::class, 'dashboard']);
    Route::get('/my_orders', [DriverOrderController::class, 'my_orders']);
    Route::get('/orders', [DriverOrderController::class, 'index']);
    Route::delete('/orders/{order}', [DriverCancelOrderController::class, 'cancel_order']);
    Route::post('/orders/{order}', [DriverGetOrderController::class, 'get_order']);
    Route::post('/orders/{order}/start', [DriverStartOrderController::class, 'start_order']);
    Route::post('/orders/{order}/stop', [DriverStoppedOrderController::class, 'stop_order']);
    Route::get('/market', [DriverMarketController::class, 'list_products']);
    Route::post('/market/{product}', [DriverMarketController::class, 'get_product']);
    Route::get('/my_products', [DriverMarketController::class, 'my_products']);
});

<?php
namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Order;
use App\Models\OrderReview;
use App\Models\Route;
use App\Models\BalanceHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class DriverDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_dashboard_returns_correct_data()
    {
        Carbon::setTestNow(Carbon::parse('2025-08-12 12:00:00'));

        $driver = Driver::factory()->create();

        $this->actingAs($driver->user, 'sanctum');

        $route = Route::factory()->create([
            'distance_km' => 100,
        ]);

        $orders = Order::factory()->count(5)->create([
            'driver_id' => $driver->id,
            'status' => 'completed',
            'route_id' => $route->id,
            'created_at' => Carbon::now()->subDays(1),
        ]);

        foreach ($orders as $order) {
            OrderReview::factory()->create([
                'order_id' => $order->id,
                'score' => 4,
                'client_id' => $order->client_id,
            ]);
        }

        BalanceHistory::factory()->create([
            'balanceable_type' => Driver::class,
            'balanceable_id' => $driver->id,
            'amount' => 250000,
            'type' => 'plus',
            'created_at' => Carbon::parse('2025-08-10 12:00:00'),
        ]);

        $response = $this->getJson('/api/driver/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_income',
                    'completed_orders_count',
                    'average_rating',
                    'recent_orders',
                ],
            ]);

        $json = $response->json('data');
        // $response->dump();

        $this->assertEquals(250000, $json['total_income']);
        $this->assertEquals(5, $json['completed_orders_count']);
        $this->assertEquals(4, $json['average_rating']);
        $this->assertCount(5, $json['recent_orders']);
    }
}
<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Order;
use App\Models\Route;
use App\Models\Taxopark;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverCancelOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateDriver()
    {
        $user = User::factory()->create();
        $taxopark = Taxopark::factory()->create();
        $driver = Driver::factory()->for($user)->for($taxopark)->create();
        $this->actingAs($user);
        return $driver;
    }

    public function test_driver_can_cancel_order()
    {
        $driver = $this->authenticateDriver();
        $client = Client::factory()->create();
        $route = Route::factory()->create([
            'taxopark_from_id' => $driver->taxopark_id,
            'taxopark_to_id' => Taxopark::factory()->create()->id,
        ]);

        $order = Order::factory()->for($driver, 'driver')->for($route)->for($client)->create([
            'status' => OrderStatus::Created,
        ]);

        $initialClientBalance = $client->balance;

        $response = $this->deleteJson("api/driver/orders/{$order->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'order_id' => $order->id,
                    'status' => 'cancelled',
                    'client_refund' => $route->deposit_client * $order->passengers,
                ],
            ]);

        $this->assertNull($order->fresh()->driver_id);
        $this->assertEquals(
            $initialClientBalance + ($route->deposit_client * $order->passengers),
            $client->fresh()->balance
        );
    }

    public function test_driver_cannot_cancel_order_of_another_driver()
    {
        $driver = $this->authenticateDriver();
        $otherDriver = Driver::factory()->for(User::factory()->create())->create();
        $client = Client::factory()->create();
        $route = Route::factory()->create([
            'taxopark_from_id' => $otherDriver->taxopark_id,
            'taxopark_to_id' => Taxopark::factory()->create()->id,
        ]);

        $order = Order::factory()->for($otherDriver, 'driver')->for($route)->for($client)->create();

        $response = $this->deleteJson("api/driver/orders/{$order->id}");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'You are not allowed to cancel this order.',
            ]);

        $this->assertNotNull($order->fresh()->driver_id);
    }
}
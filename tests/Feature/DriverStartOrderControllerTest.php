<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Enums\OrderStatus;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Order;
use App\Models\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverStartOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function driver_can_start_own_accepted_order()
    {
        $driver = Driver::factory()->create();
        $client = Client::factory()->create();
        $route = Route::factory()->create();

        $order = Order::factory()->create([
            'driver_id' => $driver->id,
            'client_id' => $client->id,
            'route_id' => $route->id,
            'status' => OrderStatus::Accepted->value,
        ]);
        $driver->user->role = 'driver';
        $driver->save();
        $this->actingAs($driver->user, 'sanctum');

        $response = $this->postJson("/api/driver/orders/{$order->id}/start");
        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'order_id' => $order->id,
                    'status' => 'started',
                ],
                'message' => 'Order successfully started.',
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::Started->value,
        ]);
    }

    #[Test]
    public function driver_cannot_start_order_of_another_driver()
    {
        $driver = Driver::factory()->create();
        $otherDriver = Driver::factory()->create();
        $client = Client::factory()->create();
        $route = Route::factory()->create();

        $order = Order::factory()->create([
            'driver_id' => $otherDriver->id,
            'client_id' => $client->id,
            'route_id' => $route->id,
            'status' => OrderStatus::Accepted->value,
        ]);
        $driver->user->role = 'driver';
        $driver->save();
        $this->actingAs($driver->user, 'sanctum');

        $response = $this->postJson("/api/driver/orders/{$order->id}/start");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => 'You are not allowed to start this order.',
            ]);
    }

    #[Test]
    public function driver_cannot_start_non_accepted_order()
    {
        $driver = Driver::factory()->create();
        $client = Client::factory()->create();
        $route = Route::factory()->create();

        $order = Order::factory()->create([
            'driver_id' => $driver->id,
            'client_id' => $client->id,
            'route_id' => $route->id,
            'status' => OrderStatus::Created->value,
        ]);
        $driver->user->role = 'driver';
        $driver->save();
        $this->actingAs($driver->user, 'sanctum');

        $response = $this->postJson("/api/driver/orders/{$order->id}/start");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error' => 'Only accepted orders can be started.',
            ]);
    }
}
<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Driver;
use App\Models\Order;
use App\Models\Route;
use App\Models\TaxoPark;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverGetOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_cannot_accept_order_if_already_assigned()
    {
        $user = User::factory()->driver()->create(['role' => 'driver']);
        $driver = $user->driver;

        $route = Route::factory()->create();
        $order = Order::factory()->create([
            'route_id' => $route->id,
            'driver_id' => $driver->id,
        ]);
        
        /** @var \App\Models\User $user */
        $this->actingAs($user);
        $response = $this->postJson("/api/driver/orders/{$order->id}");
        $response->assertStatus(400)
                 ->assertJson([
                    'success' => false,
                    'error' => 'This order already has a driver assigned.'
                ]);
    }

    public function test_driver_cannot_accept_order_if_route_mismatch()
    {
        $user = User::factory()->create(['role' => 'driver']);
        $driver = Driver::factory()->create([
            'user_id' => $user->id,
            'taxopark_id' => TaxoPark::factory()->create()->id,
        ]);

        /** @var \App\Models\User $user */
        $this->actingAs($user);
        $taxoparkFrom = TaxoPark::factory()->create();
        $taxoparkTo   = Taxopark::factory()->create();

        $route = Route::factory()->create([
            'taxopark_from_id' => $taxoparkFrom->id,
            'taxopark_to_id' => $taxoparkTo->id,
        ]);
        $order = Order::factory()->create([
            'route_id' => $route->id,
            'driver_id' => null,
            'driver_payment' => 10000,
        ]);
        $response = $this->postJson("/api/driver/orders/{$order->id}");

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'error' => 'You cannot take this order because it is not in your taxopark route.'
                ]);
    }
    public function test_driver_cannot_accept_order_if_insufficient_balance()
    {
        $user = User::factory()->driver()->create(['role' => 'driver']);
        $driver = $user->driver;
        $driver->update(['balance' => 500]);

        $route = Route::factory()->create([
            'taxopark_from_id' => $driver->taxopark_id,
            'taxopark_to_id' => TaxoPark::factory()->create()->id,
        ]);

        $order = Order::factory()->create([
            'route_id' => $route->id,
            'driver_id' => null,
            'driver_payment' => 1000,
        ]);

        /** @var \App\Models\User $user */
        $this->actingAs($user);
        $response = $this->postJson("/api/driver/orders/{$order->id}");
        $response->assertStatus(400)
                 ->assertJson([
                    'success' => false,
                    'error' => 'Insufficient balance to accept this order.'
                ]);
    }

    public function test_driver_can_accept_order_successfully()
    {
        $user = User::factory()->driver()->create(['role' => 'driver']);
        $driver = $user->driver;
        $driver->update(['balance' => 5000]);

        $route = Route::factory()->create([
            'taxopark_from_id' => $driver->taxopark_id,
            'taxopark_to_id' => TaxoPark::factory()->create()->id,
        ]);

        $order = Order::factory()->create([
            'route_id' => $route->id,
            'driver_id' => null,
            'driver_payment' => 1000,
        ]);
        
        /** @var \App\Models\User $user */
        $this->actingAs($user);
        $response = $this->postJson("/api/driver/orders/{$order->id}");
        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => ['id', 'driver' => ["id"], 'status']])
                 ->assertJson([
                    'success' => true,
                    'message' => 'Order successfully accepted.'
                ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'driver_id' => $driver->id,
            'status' => OrderStatus::Accepted,
        ]);

        $this->assertDatabaseHas('balance_histories', [
            'balanceable_id' => $driver->id,
            'type' => 'minus',
        ]);

        $this->assertDatabaseHas('order_histories', [
            'order_id' => $order->id,
            'changed_by_type' => get_class($driver),
            'changed_by_id' => $driver->id,
            'status' => OrderStatus::Accepted,
        ]);
    }
}
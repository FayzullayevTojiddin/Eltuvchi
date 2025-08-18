<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Enums\OrderStatus;
use App\Events\OrderChangedSendMessageEvent;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Order;
use App\Models\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DriverStoppedOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function driver_can_stop_own_started_order()
    {
        Event::fake();
        $driver = Driver::factory()->create();
        $client = Client::factory()->create();
        $route = Route::factory()->create();

        $order = Order::factory()->create([
            'driver_id' => $driver->id,
            'client_id' => $client->id,
            'route_id' => $route->id,
            'status' => OrderStatus::Started,
        ]);

        $driver->user->role = 'driver';
        $driver->save();

        $this->actingAs($driver->user, 'sanctum');

        $response = $this->postJson("/api/driver/orders/{$order->id}/stop");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => "Order successfully stopped.",
                'data' => [
                    'order_id' => $order->id,
                    'status' => 'stopped',
                ],
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::Stopped->value,
        ]);

        Event::assertDispatched(OrderChangedSendMessageEvent::class, function ($event) use ($order, $driver) {
            return $event->user->id === $driver->user->id &&
                   str_contains($event->message, (string)$order->id);
        });
    }

    #[Test]
    public function driver_cannot_stop_order_of_another_driver()
    {
        $driver = Driver::factory()->create();
        $otherDriver = Driver::factory()->create();
        $client = Client::factory()->create();
        $route = Route::factory()->create();

        
        $driver->user->role = 'driver';
        $driver->save();

        $order = Order::factory()->create([
            'driver_id' => $otherDriver->id,
            'client_id' => $client->id,
            'route_id' => $route->id,
            'status' => OrderStatus::Started,
        ]);

        $this->actingAs($driver->user, 'sanctum');

        $response = $this->postJson("/api/driver/orders/{$order->id}/stop");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => 'You are not allowed to stop this order.',
            ]);
    }

    #[Test]
    public function driver_cannot_stop_order_that_is_not_started()
    {
        $driver = Driver::factory()->create();
        $client = Client::factory()->create();
        $route = Route::factory()->create();
        $order = Order::factory()->create([
            'driver_id' => $driver->id,
            'client_id' => $client->id,
            'route_id' => $route->id,
            'status' => OrderStatus::Accepted,
        ]); 
        $driver->user->role = 'driver';
        $driver->save();
        $this->actingAs($driver->user, 'sanctum');
        $response = $this->postJson("/api/driver/orders/{$order->id}/stop");
        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error' => 'Only started orders can be stopped.',
            ]);
    }
}
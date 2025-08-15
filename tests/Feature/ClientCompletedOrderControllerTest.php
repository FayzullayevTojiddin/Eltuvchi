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

class ClientCompletedOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function client_can_complete_own_stopped_order()
    {
        Event::fake();
        $client = Client::factory()->create();
        $driver = Driver::factory()->create([
            'balance' => 0
        ]);
        $route = Route::factory()->create();

        $order = Order::factory()->for($client)->for($driver)->for($route)->create([
            'status' => OrderStatus::Stopped,
            'client_deposit' => 20000,
        ]);

        $this->actingAs($client->user, 'sanctum');

        $response = $this->postJson("/api/client/orders/{$order->id}/complete");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'order_id' => $order->id,
                    'status' => 'completed',
                    'driver_credited' => 20000,
                ],
                'message' => 'Order successfully completed.',
            ]);

        $this->assertEquals(OrderStatus::Completed, $order->fresh()->status);
        $this->assertEquals(20000, $driver->fresh()->balance);

        Event::assertDispatched(OrderChangedSendMessageEvent::class, function ($event) use ($driver, $order) {
            return $event->user->id === $driver->user->id
                && str_contains($event->message, (string) $order->id);
        });
    }

    #[Test]
    public function client_cannot_complete_order_of_another_client()
    {
        $client = Client::factory()->create();
        $otherClient = Client::factory()->create();
        $driver = Driver::factory()->create();
        $route = Route::factory()->create();

        $order = Order::factory()->for($otherClient)->for($driver)->for($route)->create([
            'status' => OrderStatus::Stopped,
        ]);

        $this->actingAs($client->user, 'sanctum');

        $response = $this->postJson("/api/client/orders/{$order->id}/complete");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => 'You are not allowed to complete this order.',
            ]);
    }

    #[Test]
    public function client_cannot_complete_order_that_is_not_stopped()
    {
        $client = Client::factory()->create();
        $driver = Driver::factory()->create();
        $route = Route::factory()->create();

        $order = Order::factory()->for($client)->for($driver)->for($route)->create([
            'status' => OrderStatus::Started,
        ]);

        $this->actingAs($client->user, 'sanctum');

        $response = $this->postJson("/api/client/orders/{$order->id}/complete");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error' => 'Only stopped orders can be completed.',
            ]);
    }
}
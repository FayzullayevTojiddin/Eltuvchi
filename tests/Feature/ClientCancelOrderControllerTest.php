<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientCancelOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cancel_order_success_created_status_returns_refund_to_client()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id, 'balance' => 0]);
        $order = Order::factory()->create([
            'client_id' => $client->id,
            'status' => OrderStatus::Created,
            'client_deposit' => 100000,
            'discount_percent' => 10,
        ]);
        $this->actingAs($user);
        $response = $this->deleteJson("/api/client/orders/{$order->id}");
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $client->refresh();
        $expectedRefund = 100000 * (1 - 10 / 100);
        $this->assertEquals($expectedRefund, $client->balance);
        $order->refresh();
        $this->assertEquals(OrderStatus::Cancelled, $order->status);
    }

    public function test_cancel_order_success_accepted_status_returns_deposit_to_driver()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $driverUser = User::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $driverUser->id, 'balance' => 0]);
        $order = Order::factory()->create([
            'client_id' => $client->id,
            'driver_id' => $driver->id,
            'status' => OrderStatus::Accepted,
            'client_deposit' => 100000,
            'discount_percent' => 0,
        ]);
        $this->actingAs($user);
        $response = $this->deleteJson("/api/client/orders/{$order->id}");
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $driver->refresh();
        $this->assertEquals(100000, $driver->balance);
        $order->refresh();
        $this->assertEquals(OrderStatus::Cancelled, $order->status);
    }

    public function test_cancel_order_not_found()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->deleteJson('/api/client/orders/99999');
        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'error' => 'Order not found.',
        ]);
    }

    public function test_cancel_order_unauthorized()
    {
        /** @var \App\Models\User $user1 */
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $client1 = Client::factory()->create(['user_id' => $user1->id]);
        $client2 = Client::factory()->create(['user_id' => $user2->id]);
        $order = Order::factory()->create([
            'client_id' => $client2->id,
            'status' => OrderStatus::Created,
            'client_deposit' => 100000,
            'discount_percent' => 0,
        ]);
        $this->actingAs($user1);
        $response = $this->deleteJson("/api/client/orders/{$order->id}");
        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'error' => 'Unauthorized.',
        ]);
    }

    public function test_cancel_order_already_cancelled()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create([
            'client_id' => $client->id,
            'status' => OrderStatus::Cancelled,
        ]);
        $this->actingAs($user);
        $response = $this->deleteJson("/api/client/orders/{$order->id}");
        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'error' => 'Order already cancelled.',
        ]);
    }
}
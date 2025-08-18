<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Enums\OrderStatus;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Order;
use App\Models\Route;
use App\Models\SuperAdmin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin()
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);
        return $this->actingAs($admin);
    }

    #[Test]
    public function admin_can_list_orders()
    {
        $orders = Order::factory()->count(3)->create();

        $this->actingAsAdmin()
            ->getJson('/api/super-admin/orders')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function admin_can_view_single_order_with_relations()
    {
        $client = Client::factory()->create();
        $driver = Driver::factory()->create();
        $route  = Route::factory()->create();

        $order = Order::factory()->create([
            'client_id' => $client->id,
            'driver_id' => $driver->id,
            'route_id'  => $route->id,
            'status'    => OrderStatus::Created,
        ]);

        $this->actingAsAdmin()
            ->getJson("/api/super-admin/orders/{$order->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $order->id)
            ->assertJsonPath('data.client.id', $client->id)
            ->assertJsonPath('data.driver.id', $driver->id)
            ->assertJsonPath('data.route.id', $route->id);
    }

    #[Test]
    public function admin_can_update_order_status()
    {
        $order = Order::factory()->create([
            'status' => OrderStatus::Created,
        ]);

        $this->actingAsAdmin()
            ->patchJson("/api/super-admin/orders/{$order->id}", [
                'status' => OrderStatus::Completed,
            ])
            ->assertOk()
            ->assertJsonPath('data.status', OrderStatus::Completed->value);

        $this->assertDatabaseHas('orders', [
            'id'     => $order->id,
            'status' => OrderStatus::Completed,
        ]);
    }
}
<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Http\Resources\ClientOrderResource;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Order;
use App\Models\Route;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClientOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $clientUser;
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        // Client user va client model yaratamiz
        $this->clientUser = User::factory()->create();
        $this->client = Client::factory()->create([
            'user_id' => $this->clientUser->id,
        ]);
    }

    #[Test]
    public function it_returns_all_orders_for_authenticated_client()
    {
        $orders = Order::factory()->count(3)->create([
            'client_id' => $this->client->id,
        ]);

        Order::factory()->create();

        $response = $this->actingAs($this->clientUser)
            ->getJson('/api/client/orders');

        $response->assertOk();

        $response->assertJson([
            'success' => true,
            'data' => ClientOrderResource::collection($orders)->response()->getData(true)['data']
        ]);

        $this->assertCount(3, $response->json('data'));
    }

    #[Test]
    public function it_can_filter_orders_by_status()
    {
        $pendingOrders = Order::factory()->count(2)->create([
            'client_id' => $this->client->id,
            'status' => OrderStatus::Created,
        ]);

        Order::factory()->count(2)->create([
            'client_id' => $this->client->id,
            'status' => OrderStatus::Completed,
        ]);

        $response = $this->actingAs($this->clientUser)
            ->getJson('/api/client/orders?status=' . OrderStatus::Created->value);

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));

        foreach ($response->json('data') as $order) {
            $this->assertEquals(OrderStatus::Created->value, $order['status']);
        }
    }

    #[Test]
    public function it_requires_authentication()
    {
        $this->getJson('/api/client/orders')
            ->assertUnauthorized();
    }

    #[Test]
    public function it_returns_single_order_by_id_for_authenticated_client()
    {
        $order = Order::factory()->create([
            'client_id' => $this->client->id,
        ]);

        $order->load(['client', 'driver', 'route', 'histories']);

        $response = $this->actingAs($this->clientUser)
            ->getJson("/api/client/orders/{$order->id}");

        $response->assertOk();

        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'status',
                'date',
                'time',
                'passengers',
                'phone',
                'driver' => ['id','user_id',],
                'route' => ['id','status',],
                'histories' => [
                    '*' => ['id', 'status']
                ],
            ],
        ]);
    }

    #[Test]
    public function it_returns_404_if_order_not_found()
    {
        $nonExistentOrderId = 999999;

        $response = $this->actingAs($this->clientUser)
            ->getJson("/api/client/orders/{$nonExistentOrderId}");

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'error' => 'Order not found.',
            'data' => []
        ]);
    }

    #[Test]
    public function it_requires_authentication_for_show()
    {
        $order = Order::factory()->create();
        $this->getJson("/api/client/orders/{$order->id}")
            ->assertUnauthorized();
    }
}
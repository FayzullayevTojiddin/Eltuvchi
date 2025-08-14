<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_returns_correct_client_data()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create([
            'user_id' => $user->id,
            'balance' => 5000,
            'points' => 120,
        ]);

        $routes = Route::factory()->count(3)->create();

        $client->orders()->createMany([
            [
                'route_id' => $routes[0]->id,
                'passengers' => 1,
                'date' => now()->toDateString(),
                'time' => now()->format('H:i:s'),
                'price_order' => 10000,
                'client_deposit' => 1000,
                'phone' => '998901234567',
                'status' => OrderStatus::Created->value,
            ],
            [
                'route_id' => $routes[1]->id,
                'passengers' => 2,
                'date' => now()->addDay()->toDateString(),
                'time' => now()->addHour()->format('H:i:s'),
                'price_order' => 15000,
                'client_deposit' => 1500,
                'phone' => '998901234568',
                'status' => OrderStatus::Completed->value,
            ],
            [
                'route_id' => $routes[2]->id,
                'passengers' => 1,
                'date' => now()->addDays(2)->toDateString(),
                'time' => now()->addHours(2)->format('H:i:s'),
                'price_order' => 8000,
                'client_deposit' => 800,
                'phone' => '998901234569',
                'status' => OrderStatus::Cancelled->value,
            ],
        ]);

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->getJson('/api/client/dashboard');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'orders_count' => 3,
                'balance' => 5000,
                'points' => 120,
            ],
        ]);
        // $response->dump();
    }
}
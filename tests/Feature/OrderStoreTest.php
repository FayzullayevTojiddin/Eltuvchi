<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\ClientDiscount;
use App\Models\Discount;
use App\Models\Route;
use App\Enums\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderStoreTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_create_order_success()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $client = Client::factory()->create([
            'user_id' => $user->id,
            'balance' => 100000,
        ]);

        $route = Route::factory()->create(['price_in' => 100000]);
        $discount = Discount::factory()->create([
            'type' => 'percent',
            'value' => 20,
        ]);
        $clientDiscount = ClientDiscount::factory()->create([
            'client_id' => $client->id,
            'discount_id' => $discount->id,
            'used' => false,
        ]);
        /** @var \App\Models\User $user */
        $this->actingAs($user);

        $response = $this->postJson('/api/client/orders', [
            'route_id' => $route->id,
            'date' => now()->format('Y-m-d'),
            'time' => now()->format('H:i'),
            'passengers' => 1,
            'phone' => '998901234567',
            'optional_phone' => null,
            'note' => 'Test order',
            'discount_id' => $discount->id,
            'client_deposit' => 50000,
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                    'client_deposit' => '50000.00',
                    'discount_percent' => 20,
                 ]);

        $this->assertDatabaseHas('orders', [
            'client_id' => $client->id,
            'route_id' => $route->id,
            'discount_percent' => 20,
            'status' => OrderStatus::Created->value,
        ]);

        $this->assertDatabaseHas('client_discounts', [
            'id' => $clientDiscount->id,
            'used' => true,
        ]);

        $client->refresh();

        $discountSumm = $route->price_in * ($discount->value / 100);

        $expectedBalance = 100000 - (50000 - $discountSumm);

        $this->assertEquals($expectedBalance, $client->balance);

        $this->assertDatabaseHas('balance_histories', [
            'balanceable_id' => $client->id,
            'balanceable_type' => get_class($client),
            'amount' => -($expectedBalance - 100000),
            'description' => 'Order deposit payment',
        ]);

        $orderId = $response->json('data.id');
        $this->assertDatabaseHas('order_histories', [
            'order_id' => $orderId,
            'status' => OrderStatus::Created->value,
            'changed_by_id' => $user->id,
        ]);
    }

    #[Test]
    public function test_create_order_fail_due_to_insufficient_balance()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id, 'balance' => 1000]);

        $route = Route::factory()->create(['price_in' => 100000]);

        /** @var \App\Models\User $user */
        $this->actingAs($user);

        $response = $this->postJson('/api/client/orders', [
            'route_id' => $route->id,
            'date' => now()->format('Y-m-d'),
            'time' => now()->format('H:i'),
            'passengers' => 1,
            'phone' => '998901234567',
            'optional_phone' => null,
            'note' => 'Test order',
            'client_deposit' => 50000,
        ]);

        $response->assertStatus(400)
                 ->assertJson(fn ($json) => 
                    $json->where('success', false)
                         ->where('data', 'Balance is insufficient for deposit payment.')
                         ->etc()
                 );

        // dump($response->json()); 
    }
}
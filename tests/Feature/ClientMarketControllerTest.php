<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ClientDiscount;
use App\Models\Discount;
use App\Models\PointHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientMarketControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_purchase_active_discount_and_points_are_deducted_and_history_created()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create([
            'user_id' => $user->id,
            'points' => 100,
        ]);

        $discount = Discount::factory()->create([
            'points' => 50,
            'status' => Discount::STATUS_ACTIVE,
        ]);

        /** @var \App\Models\User $user */
        $this->actingAs($user);

        $response = $this->postJson('/api/client/market', [
            'discount_id' => $discount->id,
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Discount purchased successfully.',
                 ]);

        $this->assertDatabaseHas('client_discounts', [
            'client_id' => $client->id,
            'discount_id' => $discount->id,
            'used' => false,
        ]);

        $client->refresh();
        $this->assertEquals(50, $client->points);

        $this->assertDatabaseHas('point_histories', [
            'pointable_id' => $client->id,
            'pointable_type' => Client::class,
            'points' => 50,
            'type' => 'minus',
            'description' => "Purchased discount ID {$discount->id}",
        ]);
    }

    public function test_cannot_purchase_inactive_discount()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create([
            'user_id' => $user->id,
            'points' => 100,
        ]);

        $discount = Discount::factory()->inactive()->create();

        /** @var \App\Models\User $user */
        $this->actingAs($user);

        $response = $this->postJson('/api/client/market', [
            'discount_id' => $discount->id,
        ]);

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'error' => 'Discount not found or inactive.',
                 ]);

        $this->assertDatabaseMissing('client_discounts', [
            'client_id' => $client->id,
            'discount_id' => $discount->id,
        ]);
    }

    public function test_cannot_purchase_if_not_enough_points()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create([
            'user_id' => $user->id,
            'points' => 10,
        ]);

        $discount = Discount::factory()->create([
            'points' => 50,
            'status' => Discount::STATUS_ACTIVE,
        ]);

        /** @var \App\Models\User $user */
        $this->actingAs($user);

        $response = $this->postJson('/api/client/market', [
            'discount_id' => $discount->id,
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                     'error' => 'You do not have enough points to purchase this discount.',
                 ]);

        $this->assertDatabaseMissing('client_discounts', [
            'client_id' => $client->id,
            'discount_id' => $discount->id,
        ]);

        $client->refresh();
        $this->assertEquals(10, $client->points);
    }
}
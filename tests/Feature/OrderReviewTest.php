<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Enums\OrderStatus;
use App\Models\User;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderReview;
use App\Models\Driver;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_create_review_successfully()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $driver = Driver::factory()->create();

        $order = Order::factory()->create([
            'client_id' => $client->id,
            'driver_id' => $driver->id,
            'status' => OrderStatus::Completed->value,
        ]);

        /** @var \App\Models\User $user */
        $this->actingAs($user);

        $response = $this->postJson(route('orders.client_review', $order), [
            'rating' => 4,
            'comment' => 'Good service',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'order_id',
                        'client_id',
                        'score',
                        'comment',
                        'created_at',
                        'updated_at',
                    ],
                 ])
                 ->assertJson([
                    'success' => true,
                    'message' => 'Review created successfully.',
                 ]);

        $this->assertDatabaseHas('order_reviews', [
            'order_id' => $order->id,
            'client_id' => $client->id,
            'score' => 4,
            'comment' => 'Good service',
        ]);
    }

    public function test_cannot_review_if_not_order_owner()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);

        $otherUser = User::factory()->create();
        $otherClient = Client::factory()->create(['user_id' => $otherUser->id]);

        $driver = Driver::factory()->create();

        $order = Order::factory()->create([
            'client_id' => $otherClient->id,
            'driver_id' => $driver->id,
            'status' => OrderStatus::Completed->value,
        ]);

        /** @var \App\Models\User $user */
        $this->actingAs($user);

        $response = $this->postJson(route('orders.client_review', $order), [
            'rating' => 5,
            'comment' => 'Nice',
        ]);

        $response->assertStatus(403)
                 ->assertJson([
                     'success' => false,
                     'error' => 'You are not allowed to review this order.',
                     'data' => [],
                 ]);
    }

    public function test_cannot_review_twice_for_same_order()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $driver = Driver::factory()->create();

        $order = Order::factory()->create([
            'client_id' => $client->id,
            'driver_id' => $driver->id,
            'status' => OrderStatus::Completed->value,
        ]);

        OrderReview::factory()->create([
            'order_id' => $order->id,
            'client_id' => $client->id,
            'score' => 4,
            'comment' => 'First review',
        ]);

        /** @var \App\Models\User $user */
        $this->actingAs($user);

        $response = $this->postJson(route('orders.client_review', $order), [
            'rating' => 3,
            'comment' => 'Second review attempt',
        ]);

        $response->assertStatus(409)
                 ->assertJson([
                    'success' => false,
                    'error' => 'You have already reviewed this order.',
                    'data' => [],
                 ]);
    }

    public function test_validation_errors_for_invalid_input()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $driver = Driver::factory()->create();

        $order = Order::factory()->create([
            'client_id' => $client->id,
            'driver_id' => $driver->id,
            'status' => OrderStatus::Completed->value,
        ]);

        /** @var \App\Models\User $user */
        $this->actingAs($user);

        $response = $this->postJson(route('orders.client_review', $order), [
            'comment' => 'No rating',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('rating');

        // Rating out of range
        $response = $this->postJson(route('orders.client_review', $order), [
            'rating' => 6,
            'comment' => 'Too high rating',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('rating');

        $longComment = str_repeat('a', 1001);
        $response = $this->postJson(route('orders.client_review', $order), [
            'rating' => 4,
            'comment' => $longComment,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('comment');
    }

    public function test_cannot_review_if_order_not_completed()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $driver = Driver::factory()->create();

        $order = Order::factory()->create([
            'client_id' => $client->id,
            'driver_id' => $driver->id,
            'status' => OrderStatus::Created->value,
        ]);

        /** @var \App\Models\User $user */
        $this->actingAs($user);

        $response = $this->postJson(route('orders.client_review', $order), [
            'rating' => 5,
            'comment' => 'Trying to review early',
        ]);

        $response->assertStatus(403)
                 ->assertJson([
                    'success' => false,
                    'error' => 'Order is not completed yet.',
                    'data' => [],
                 ]);
    }
}
<?php

namespace Tests\Feature;

use App\Models\BalanceHistory;
use App\Models\Client;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BalanceHistoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_balance_history_returns_data()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);

        BalanceHistory::factory()->count(3)->create([
            'balanceable_id' => $client->id,
            'balanceable_type' => Client::class,
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/client/histories');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'amount',
                    'type',
                    'balance_after',
                    'description',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_driver_balance_history_returns_data()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id]);

        BalanceHistory::factory()->count(2)->create([
            'balanceable_id' => $driver->id,
            'balanceable_type' => Driver::class,
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/driver/histories');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'amount',
                    'type',
                    'balance_after',
                    'description',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

        $this->assertCount(2, $response->json('data'));
    }
}
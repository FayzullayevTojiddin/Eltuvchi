<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use App\Models\Client;
use App\Models\Discount;
use App\Models\ClientDiscount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ClientDiscountControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_get_active_discounts()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);

        $discount1 = Discount::factory()->create();
        $discount2 = Discount::factory()->create();

        ClientDiscount::factory()->create([
            'client_id' => $client->id,
            'discount_id' => $discount1->id,
            'used' => false,
        ]);

        ClientDiscount::factory()->create([
            'client_id' => $client->id,
            'discount_id' => $discount2->id,
            'used' => true,
        ]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('/api/client/my_discounts');
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment([
            'id' => $discount1->id,
            'title' => $discount1->title,
        ]);
    }
}
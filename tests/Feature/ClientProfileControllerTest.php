<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ClientProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_client_profile_when_authenticated()
    {
        $user = User::factory()->create([
            'role' => 'client',
            'telegram_id' => '123456789',
        ]);

        $client = Client::factory()->create([
            'user_id' => $user->id,
            'settings' => [
                'full_name'     => 'Test User',
                'phone_number'  => '998901234567',
                'notifications' => true,
                'night_mode'    => false,
                'language'      => 'uz',
            ],
        ]);

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)
            ->getJson('/api/client/me');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'role' => 'client',
                    'telegram_id' => '123456789',
                    'settings' => $client->settings,
                ],
                'message' => 'Client profile retrieved successfully.',
            ]);
    }

    #[Test]
    public function it_returns_404_if_client_not_found()
    {
        $user = User::factory()->create([
            'role' => 'client',
        ]);

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)
            ->getJson('/api/client/me');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'error' => 'Client not found.',
            ]);
    }

    #[Test]
    public function it_returns_unauthorized_if_not_authenticated()
    {
        $response = $this->getJson('/api/client/me');

        $response->assertStatus(401);
    }
}
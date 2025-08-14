<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ClientProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_client_profile_settings()
    {
        $user = User::factory()->create([
            'role' => 'client',
            'telegram_id' => '123456789',
        ]);

        Client::factory()->create([
            'user_id' => $user->id,
            'settings' => [
                'full_name'     => 'Old Name',
                'phone_number'  => '998900000000',
                'notifications' => true,
                'night_mode'    => false,
                'language'      => 'uz',
            ]
        ]);
        $payload = [
            'full_name'     => 'New Name',
            'phone_number'  => '998911112233',
            'notifications' => false,
            'night_mode'    => true,
            'language'      => 'ru',
        ];

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)
            ->putJson('/api/client/me', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Client profile updated successfully.',
                'data' => [
                    'id' => $user->id,
                    'role' => 'client',
                    'telegram_id' => '123456789',
                    'settings' => $payload,
                ]
            ]);

        $this->assertDatabaseHas('clients', [
            'user_id' => $user->id,
        ]);
    }
}
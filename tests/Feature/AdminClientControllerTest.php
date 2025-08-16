<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminClientControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin()
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        return $this->actingAs($admin);
    }

    #[Test]
    public function it_can_list_clients()
    {
        Client::factory()->count(3)->create();

        $this->actingAsAdmin()
            ->getJson('/api/super-admin/clients')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'status', 'balance', 'points', 'user'],
                ],
            ]);
    }

    #[Test]
    public function it_can_show_a_single_client()
    {
        $client = Client::factory()->create();

        $this->actingAsAdmin()
            ->getJson("/api/super-admin/clients/{$client->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $client->id);
    }

    #[Test]
    public function it_can_update_a_client()
    {
        $client = Client::factory()->create(['status' => 'active']);

        $this->actingAsAdmin()
            ->putJson("/api/super-admin/clients/{$client->id}", [
                'status' => 'vip',
                'balance' => 5000,
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'vip')
            ->assertJsonPath('data.balance', 5000);
    }

    #[Test]
    public function it_can_mark_client_as_inactive_instead_of_deleting()
    {
        $client = Client::factory()->create(['status' => 'active']);

        $this->actingAsAdmin()
            ->deleteJson("/api/super-admin/clients/{$client->id}")
            ->assertOk()
            ->assertJsonPath('data.status', 'inactive');

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'status' => 'inactive',
        ]);
    }
}
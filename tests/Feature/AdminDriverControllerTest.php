<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDriverControllerTest extends TestCase
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
    public function it_can_list_drivers()
    {
        Driver::factory()->count(3)->create();

        $this->actingAsAdmin()
            ->getJson('/api/super-admin/drivers')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'status', 'balance', 'points', 'user', 'taxopark'],
                ],
            ]);
    }

    #[Test]
    public function it_can_show_a_single_driver()
    {
        $driver = Driver::factory()->create();

        $this->actingAsAdmin()
            ->getJson("/api/super-admin/drivers/{$driver->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $driver->id);
    }

    #[Test]
    public function it_can_update_a_driver()
    {
        $driver = Driver::factory()->create([
            'status' => 'active',
            'balance' => 1000,
            'points' => 10,
        ]);

        $this->actingAsAdmin()
            ->putJson("/api/super-admin/drivers/{$driver->id}", [
                'status' => 'vip',
                'balance' => 5000,
                'points' => 50,
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'vip')
            ->assertJsonPath('data.balance', 5000)
            ->assertJsonPath('data.points', 50);
    }

    #[Test]
    public function it_can_mark_driver_as_inactive_instead_of_deleting()
    {
        $driver = Driver::factory()->create([
            'status' => 'active',
        ]);

        $this->actingAsAdmin()
            ->deleteJson("/api/super-admin/drivers/{$driver->id}")
            ->assertOk()
            ->assertJsonPath('data.status', 'inactive');

        $this->assertDatabaseHas('drivers', [
            'id' => $driver->id,
            'status' => 'inactive',
        ]);
    }
}
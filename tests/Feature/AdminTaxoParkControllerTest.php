<?php

namespace Tests\Feature;

use App\Models\Dispatcher;
use App\Models\Driver;
use PHPUnit\Framework\Attributes\Test;
use App\Models\TaxoPark;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTaxoParkControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin()
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create([
            'role' => 'admin', // optional, test uchun
        ]);

        return $this->actingAs($admin);
    }

    #[Test]
    public function it_can_list_taxoparks()
    {
        $region = Region::factory()->create();
        TaxoPark::factory()->count(3)->create(['region_id' => $region->id]);

        $this->actingAsAdmin()
            ->getJson('/api/super-admin/taxoparks')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'status', 'region'],
                ],
            ]);
    }

    #[Test]
    public function it_can_show_a_single_taxopark_with_drivers_and_dispatchers()
    {
        $region = Region::factory()->create();
        $taxopark = TaxoPark::factory()->create(['region_id' => $region->id]);

        $drivers = Driver::factory()->count(2)->create(['taxopark_id' => $taxopark->id]);
        $dispatchers = Dispatcher::factory()->count(2)->create(['taxopark_id' => $taxopark->id]);

        $this->actingAsAdmin()
            ->getJson("/api/super-admin/taxoparks/{$taxopark->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $taxopark->id)
            ->assertJsonCount(2, 'data.drivers')
            ->assertJsonCount(2, 'data.dispatchers');
    }

    #[Test]
    public function it_can_update_a_taxopark()
    {
        $region = Region::factory()->create();
        $taxopark = TaxoPark::factory()->create([
            'name' => 'Old Name',
            'status' => 'active',
            'region_id' => $region->id
        ]);

        $this->actingAsAdmin()
            ->putJson("/api/super-admin/taxoparks/{$taxopark->id}", [
                'name' => 'New Name',
                'status' => 'inactive',
                'region_id' => $region->id
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'New Name')
            ->assertJsonPath('data.status', 'inactive');
    }

    #[Test]
    public function it_can_mark_taxopark_as_inactive_instead_of_deleting()
    {
        $taxopark = TaxoPark::factory()->create(['status' => 'active']);

        $this->actingAsAdmin()
            ->deleteJson("/api/super-admin/taxoparks/{$taxopark->id}")
            ->assertOk()
            ->assertJsonPath('data.status', 'inactive');

        $this->assertDatabaseHas('taxo_parks', [
            'id' => $taxopark->id,
            'status' => 'inactive',
        ]);
    }
}

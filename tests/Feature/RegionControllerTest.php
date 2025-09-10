<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Region;
use App\Models\TaxoPark;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_regions()
    {
        Region::factory()->count(3)->create();

        $response = $this->getJson('/api/regions');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name']
            ],
        ]);
    }

    public function test_show_returns_region_with_taxoparks()
    {
        $region = Region::factory()->create();

        TaxoPark::factory()->count(2)->create(['region_id' => $region->id]);

        $response = $this->getJson("/api/regions/{$region->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'region' => ['id', 'name'],
                'taxoparks' => [
                    '*' => ['id', 'name']
                ],
            ],
        ]);
    }

    public function test_show_returns_404_if_region_not_found()
    {
        $response = $this->getJson('/api/regions/999999');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'error' => 'Region not found.',
            'data' => [],
        ]);
    }
}
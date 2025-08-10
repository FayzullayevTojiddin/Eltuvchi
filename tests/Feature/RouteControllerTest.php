<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Route;
use App\Models\TaxoPark;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_returns_route_data_if_exists()
    {
        $from = TaxoPark::factory()->create();
        $to = TaxoPark::factory()->create();

        $route = Route::factory()->create([
            'taxopark_from_id' => $from->id,
            'taxopark_to_id' => $to->id,
        ]);

        $response = $this->getJson("/api/routes/check/{$from->id}/{$to->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'from',
                'to',
                'status',
                'deposit_client',
                'distance_km',
                'price_in',
                'fee_per_client',
            ]
        ]);
    }

    public function test_check_returns_error_if_route_not_found()
    {
        $from = TaxoPark::factory()->create();
        $to = TaxoPark::factory()->create();

        $response = $this->getJson("/api/routes/check/{$from->id}/{$to->id}");

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'error' => 'Route not found.',
            'data' => [],
        ]);
    }

    public function test_check_returns_404_if_route_is_inactive()
    {
        $from = \App\Models\TaxoPark::factory()->create();
        $to = \App\Models\TaxoPark::factory()->create();

        \App\Models\Route::factory()->create([
            'taxopark_from_id' => $from->id,
            'taxopark_to_id' => $to->id,
            'status' => \App\Enums\RouteStatus::INACTIVE->value,
        ]);

        $response = $this->getJson("/api/routes/check/{$from->id}/{$to->id}");

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'error' => 'Route not found.',
            'data' => [],
        ]);
    }
}
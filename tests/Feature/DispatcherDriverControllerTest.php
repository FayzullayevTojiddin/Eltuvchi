<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\Dispatcher;
use App\Models\Driver;
use App\Models\TaxoPark;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DispatcherDriverControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Dispatcher $dispatcher;
    protected TaxoPark $taxopark;
    protected User $dispatcherUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taxopark = TaxoPark::factory()->create();
        $this->dispatcherUser = User::factory()->create(['role' => 'dispatcher']);
        $this->dispatcher = Dispatcher::factory()->create([
            'user_id' => $this->dispatcherUser->id,
            'taxopark_id' => $this->taxopark->id,
        ]);
    }

    #[Test]
    public function index_returns_drivers_filtered_by_status()
    {
        $activeDriver = Driver::factory()->create([
            'taxopark_id' => $this->taxopark->id,
            'status' => 'active',
        ]);

        $inactiveDriver = Driver::factory()->create([
            'taxopark_id' => $this->taxopark->id,
            'status' => 'inactive',
        ]);

        $response = $this->actingAs($this->dispatcherUser)
                         ->getJson('api/dispatcher/drivers?status=active');
        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $activeDriver->id])
                 ->assertJsonMissing(['id' => $inactiveDriver->id]);
    }

    #[Test]
    public function show_returns_driver_when_same_taxopark()
    {
        $driver = Driver::factory()->create([
            'taxopark_id' => $this->taxopark->id,
        ]);

        $response = $this->actingAs($this->dispatcherUser, 'sanctum')
                         ->getJson("api/dispatcher/drivers/{$driver->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $driver->id]);
    }

    #[Test]
    public function show_returns_403_for_other_taxopark_driver()
    {
        $otherTaxoparkDriver = Driver::factory()->create();

        $response = $this->actingAs($this->dispatcherUser, 'sanctum')
                         ->getJson("api/dispatcher/drivers/{$otherTaxoparkDriver->id}");

        $response->assertStatus(403);
    }

    #[Test]
    public function store_creates_driver_for_dispatchers_taxopark()
    {
        $user = User::factory()->create();

        $payload = [
            'user_id' => $user->id,
            'status' => 'active',
            'balance' => 1000,
            'points' => 50,
        ];

        $response = $this->actingAs($this->dispatcherUser, 'sanctum')
                         ->postJson('api/dispatcher/drivers', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['user_id' => $user->id]);

        $this->assertDatabaseHas('drivers', [
            'user_id' => $user->id,
            'taxopark_id' => $this->taxopark->id,
        ]);
    }

    #[Test]
    public function update_modifies_driver_only_if_same_taxopark()
    {
        $driver = Driver::factory()->create([
            'taxopark_id' => $this->taxopark->id,
            'status' => 'active',
        ]);

        $payload = ['status' => 'inactive', 'balance' => 500];

        $response = $this->actingAs($this->dispatcherUser, 'sanctum')
                         ->putJson("api/dispatcher/drivers/{$driver->id}", $payload);

        $response->assertStatus(200)
                 ->assertJsonFragment(['status' => 'inactive', 'balance' => 500]);
    }

    #[Test]
    public function destroy_sets_driver_status_to_inactive()
    {
        $driver = Driver::factory()->create([
            'taxopark_id' => $this->taxopark->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->dispatcherUser, 'sanctum')
                         ->deleteJson("api/dispatcher/drivers/{$driver->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('drivers', [
            'id' => $driver->id,
            'status' => 'inactive',
        ]);
    }
}
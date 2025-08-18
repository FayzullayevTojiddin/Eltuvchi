<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Order;
use App\Models\Route;
use App\Models\Taxopark;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateDriver()
    {
        $user = User::factory()->create(['role' => 'driver']);
        $taxopark = Taxopark::factory()->create();
        $driver = Driver::factory()->for($user)->for($taxopark)->create();

        /** @var \App\Models\User $user */
        $this->actingAs($user);

        return $driver;
    }

    public function test_driver_can_get_all_orders()
    {
        $driver = $this->authenticateDriver();
        $taxopark = $driver->taxopark;

        $client = Client::factory()->create();
        $route = Route::factory()->create([
            'taxopark_from_id' => $taxopark->id,
            'taxopark_to_id' => Taxopark::factory()->create()->id,
        ]);

        Order::factory()->for($driver, 'driver')->for($route)->for($client)->create();
        Order::factory()->for($driver, 'driver')->for($route)->for($client)->create();

        $response = $this->actingAs($driver->user)->getJson('api/driver/my_orders');
        $response->assertOk()
                ->assertJsonCount(2, 'data')
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'status',
                            'date',
                            'time',
                            'passengers',
                            'phone',
                            'optional_phone',
                            'note',
                            'price_order',
                            'client_deposit',
                            'discount_percent',
                            'discount_summ',
                            'route',
                            'review',
                            'histories',
                        ],
                    ],
                ]);
    }

    public function test_driver_can_filter_orders_by_status()
    {
        $driver = $this->authenticateDriver();
        $taxopark = $driver->taxopark;
        $client = Client::factory()->create();
        $route = Route::factory()->create([
            'taxopark_from_id' => $taxopark->id,
            'taxopark_to_id' => Taxopark::factory()->create()->id,
        ]);

        Order::factory()->for($driver, 'driver')->for($route)->for($client)
            ->create(['status' => OrderStatus::Created]);

        Order::factory()->for($driver, 'driver')->for($route)->for($client)
            ->create(['status' => OrderStatus::Completed]);
        $response = $this->getJson('api/driver/my_orders?status=' . OrderStatus::Created->value);
        // $response->dump();

        $response->assertOk()
                ->assertJsonCount(1, 'data')
                ->assertJsonFragment(['status' => OrderStatus::Created->value]);
    }
}
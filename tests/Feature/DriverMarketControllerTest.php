<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Driver;
use App\Models\Product;
use App\Models\DriverProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverMarketControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateDriver(int $points = 100): Driver
    {
        $user = User::factory()->create();
        $driver = Driver::factory()->create([
            'user_id' => $user->id,
            'points'  => $points,
        ]);

        /** @var \App\Models\User $user */
        $this->actingAs($user);

        return $driver;
    }

    #[Test]
    public function it_returns_only_active_products()
    {
        $this->authenticateDriver();

        $activeProduct   = Product::factory()->create(['status' => true]);
        $inactiveProduct = Product::factory()->create(['status' => false]);

        $response = $this->getJson('/api/driver/market');

        $response->assertOk()
                 ->assertJsonFragment(['id' => $activeProduct->id])
                 ->assertJsonMissing(['id' => $inactiveProduct->id]);
    }

    #[Test]
    public function driver_cannot_purchase_if_not_enough_points()
    {
        $driver = $this->authenticateDriver(points: 5);
        $product = Product::factory()->create([
            'status' => true,
            'points' => 10,
        ]);

        $response = $this->postJson("/api/driver/market/{$product->id}");

        $response->assertStatus(422)
                 ->assertJsonFragment(['error' => 'Insufficient points.']);

        $this->assertDatabaseMissing('driver_products', [
            'driver_id'  => $driver->id,
            'product_id' => $product->id,
        ]);
    }

    #[Test]
    public function driver_can_purchase_product_with_enough_points()
    {
        $driver = $this->authenticateDriver(points: 50);
        $product = Product::factory()->create([
            'status' => true,
            'points' => 30,
        ]);

        $response = $this->postJson("/api/driver/market/{$product->id}");

        $response->assertOk()
                 ->assertJsonFragment(['message' => 'Product successfully purchased.'])
                 ->assertJsonFragment(['id' => $product->id]);

        $this->assertDatabaseHas('driver_products', [
            'driver_id'  => $driver->id,
            'product_id' => $product->id,
        ]);

        $this->assertDatabaseHas('point_histories', [
            'pointable_id'   => $driver->id,
            'pointable_type' => Driver::class,
            'points'         => 30,
            'type'           => 'minus',
        ]);

        $driver->refresh();
        $this->assertEquals(20, $driver->points);
    }
}
<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\Product;
use App\Models\Discount;
use App\Models\SuperAdmin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminMarketControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create([
            'role' => 'admin'
        ]);
        SuperAdmin::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);
    }

    #[Test]
    public function it_returns_products_list()
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/super-admin/market?type=product');

        $response->assertOk()
                 ->assertJsonStructure([
                    'data' => [
                        '*' => ['id', 'title', 'description', 'status', 'points']
                    ]
                 ]);
    }

    #[Test]
    public function it_returns_discounts_list()
    {
        Discount::factory()->count(2)->create();

        $response = $this->getJson('/api/super-admin/market?type=discount');

        $response->assertOk()
                 ->assertJsonStructure([
                    'data' => [
                        '*' => ['id', 'title', 'points']
                    ]
                 ]);
    }

    #[Test]
    public function it_returns_error_for_invalid_type_in_index()
    {
        $response = $this->getJson('/api/super-admin/market?type=wrong');

        $response->assertStatus(400)
                 ->assertJson(['error' => 'Invalid type, must be product or discount']);
    }

    #[Test]
    public function it_creates_a_product()
    {
        $data = [
            'type' => 'product',
            'title' => 'Test Product',
            'description' => 'Some description',
            'status' => true,
            'icon_type' => 'star',
            'points' => 50,
        ];

        $response = $this->postJson('/api/super-admin/market', $data);

        $response->assertCreated()
                 ->assertJsonFragment(['title' => 'Test Product']);

        $this->assertDatabaseHas('products', ['title' => 'Test Product']);
    }

    #[Test]
    public function it_creates_a_discount()
    {
        $data = [
            'type' => 'discount',
            'title' => 'Test Discount',
            'points' => 100,
            'value' => 10,
            'percent' => 5,
            'active' => true,
            'icon' => 'gift',
        ];

        $response = $this->postJson('/api/super-admin/market', $data);

        $response->assertCreated()
                 ->assertJsonFragment(['title' => 'Test Discount']);

        $this->assertDatabaseHas('discounts', ['title' => 'Test Discount']);
    }

    #[Test]
    public function it_updates_a_product()
    {
        $product = Product::factory()->create();

        $response = $this->putJson("/api/super-admin/market/{$product->id}?type=product", [
            'title' => 'Updated Product',
            'description' => 'Updated description',
            'status' => false,
        ]);

        $response->assertOk()
                 ->assertJsonFragment(['title' => 'Updated Product']);

        $this->assertDatabaseHas('products', ['title' => 'Updated Product']);
    }

    #[Test]
    public function it_updates_a_discount()
    {
        $discount = Discount::factory()->create();

        $response = $this->putJson("/api/super-admin/market/{$discount->id}?type=discount", [
            'title' => 'Updated Discount',
            'status' => false,
        ]);

        $response->assertOk()
                 ->assertJsonFragment(['title' => 'Updated Discount']);

        $this->assertDatabaseHas('discounts', ['title' => 'Updated Discount']);
    }

    #[Test]
    public function it_returns_error_for_invalid_type_in_update()
    {
        $product = Product::factory()->create();

        $response = $this->putJson("/api/super-admin/market/{$product->id}?type=wrong", [
            'title' => 'Invalid Update'
        ]);

        $response->assertStatus(400)
                 ->assertJson(['error' => 'Invalid type, must be product or discount']);
    }
}
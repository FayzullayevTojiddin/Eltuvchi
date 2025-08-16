<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Enums\OrderStatus;
use App\Models\Driver;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_dashboard_statistics()
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        Driver::factory()->count(3)->create();
        User::factory()->count(5)->create(['role' => 'client']);
        User::factory()->create(['role' => 'dispatcher']);
        User::factory()->create(['role' => 'admin']);

        Order::factory()->count(2)->create([
            'status' => OrderStatus::Completed,
            'driver_payment' => 1000,
            'created_at' => Carbon::today(),
        ]);

        Order::factory()->count(1)->create([
            'status' => OrderStatus::Completed,
            'driver_payment' => 500,
            'created_at' => Carbon::now()->subWeek(),
        ]);

        Order::factory()->count(4)->create();

        $response = $this->getJson('/api/super-admin/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                        'counts' => [
                        'drivers',
                        'clients',
                        'dispatchers',
                        'admins',
                    ],
                    'orders' => [
                        'today',
                        'week',
                        'month',
                        'total',
                    ],
                    'revenue' => [
                        'today',
                        'week',
                        'month',
                        'total',
                    ],
                ]
            ]);

        $this->assertEquals(
            2000,
            $response->json('data.revenue.today')
        );
    }
}
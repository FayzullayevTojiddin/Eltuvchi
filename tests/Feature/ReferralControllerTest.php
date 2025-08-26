<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Referral;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ReferralControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_referral_statistics_for_authenticated_user(): void
    {
        $referrer = User::factory()->create();

        $users = User::factory()->count(3)->create();

        foreach ($users as $user) {
            Referral::factory()->create([
                'user_id' => $user->id,
                'referred_by' => $referrer->id,
                'created_at' => now(),
            ]);
        }

        /** @var \App\Models\User $referrer */
        $response = $this->actingAs($referrer)->getJson('/api/referrals');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'statistics' => ['total', 'this_month', 'this_week'],
                    'referrers',
                    'points',
                    'point_histories',
                ],
            ])
            ->assertJson(fn ($json) =>
                $json->where('success', true)
                    ->where('message', 'Referral statistics and points retrieved successfully.')
                    ->where('data.statistics.total', 3)
                    ->where('data.statistics.this_month', 3)
                    ->where('data.statistics.this_week', 3)
                    ->has('data.referrers', 3)
                    ->where('data.points', 0)
                    ->has('data.point_histories', 0)
            );
            // $response->dump();
    }

    #[Test]
    public function it_returns_unauthorized_for_guest_user(): void
    {
        $response = $this->getJson('/api/referrals');

        $response->assertStatus(401);
    }

    #[Test]
    public function it_allows_authenticated_user_to_refer_with_valid_promo_code(): void
    {
        $referrer = User::factory()->create();

        Referral::factory()->create([
            'user_id' => $referrer->id,
            'promo_code' => $referrer->promo_code,
        ]);

        $user = User::factory()->create();

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->postJson('/api/referrals', [
            'promo_code' => $referrer->promo_code,
        ]);

        // $response->dump();

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Referral added successfully',
            ]);

        $this->assertDatabaseHas('referrals', [
            'user_id' => $user->id,
            'referred_by' => $referrer->id,
        ]);
    }

    #[Test]
    public function it_prevents_referral_with_invalid_promo_code(): void
    {
        $user = User::factory()->create();

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->postJson('/api/referrals', [
            'promo_code' => 'INVALID',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Invalid promo code',
            ]);
    }

    #[Test]
    public function it_prevents_multiple_referrals_for_same_user(): void
    {
        $referrer = User::factory()->create(['promo_code' => 'PROMO1234']);

        Referral::factory()->create([
            'user_id' => $referrer->id,
            'promo_code' => 'PROMO1234',
        ]);

        $user = User::factory()->create();

        Referral::factory()->create([
            'user_id' => $user->id,
            'referred_by' => $referrer->id,
        ]);

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->postJson('/api/referrals', [
            'promo_code' => 'PROMO1234',
        ]);

        // $response->dump();

        $response->assertStatus(409)
            ->assertJson([
                'message' => 'You have already been referred',
            ]);
    }
}
<?php

namespace Tests\Feature\Account;

use App\Models\Account;
use App\Models\User;
use Arr;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Carbon;

class ConfirmTest extends TestCase
{
    public function test_controller_case_not_oke()
    {
        $user = $this->factoryUser();
        $account = Account::factory()->state([
            'bought_at' => now(),
            'confirmed_at' => now()->addMinutes(20),
            'buyer_id' => $user->getKey(),
        ])->create();
        $router = route('accounts.confirm', ['account' => $account]);
        $data = [
            'oke' => false,
        ];

        $this->actingAs($user)
            ->json('patch', $router, $data)
            ->assertStatus(200);

        $this->assertDatabaseHas('accounts', [
            'confirmed_at' => null,
            'id' => $account->getKey(),
        ]);
    }

    public function test_controller_case_oke()
    {
        $user = $this->factoryUser();
        $account = Account::factory()->state([
            'bought_at' => now(),
            'confirmed_at' => now()->addMinutes(20),
            'buyer_id' => $user->getKey(),
        ])->create();
        $router = route('accounts.confirm', ['account' => $account]);
        $data = [
            'oke' => true,
        ];

        $this->actingAs($user)
            ->json('patch', $router, $data)
            ->assertStatus(200);

        $this->assertTrue(now()->gte($account->refresh()->confirmed_at));
    }

    public function test_controller_case_was_confirmed()
    {
        $user = $this->factoryUser();
        $account = Account::factory()->state([
            'bought_at' => now()->subHours(2),
            'confirmed_at' => $confirmedAt = now()->subHour()->roundSecond(),
            'buyer_id' => $user->getKey(),
        ])->create();
        $router = route('accounts.confirm', ['account' => $account]);
        $data = [
            'oke' => Arr::random([true, false]),
        ];

        $this->actingAs($user)
            ->json('patch', $router, $data)
            ->assertStatus(403);

        $this->assertEquals($confirmedAt, $account->refresh()->confirmed_at);
    }

    public function test_middleware_not_buyer()
    {
        $user = $this->factoryUser();
        $account = Account::factory()->state([
            'bought_at' => now()->subHours(2),
            'confirmed_at' => now()->subHour()->roundSecond(),
        ])
            ->for(User::factory(), 'buyer')
            ->create();
        $router = route('accounts.confirm', ['account' => $account]);

        $this->actingAs($user)
            ->json('patch', $router)
            ->assertStatus(403);
    }
}

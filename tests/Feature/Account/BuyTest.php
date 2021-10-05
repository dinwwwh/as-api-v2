<?php

namespace Tests\Feature\Account;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BuyTest extends TestCase
{
    public function test_controller()
    {
        $user = $this->factoryUser(inverse: true, user: User::factory()->state([
            'balance' => 10000,
        ])->create());
        $account = Account::factory()->state([
            'price' => 10000,
            'bought_at' => null,
        ])->create();
        $router = route('accounts.buy', ['account' => $account]);

        $this->actingAs($user)
            ->json('patch', $router)
            ->assertStatus(200);

        $this->assertDatabaseHas('accounts', [
            'id' => $account->getKey(),
            'buyer_id' => $user->getKey(),
            'bought_at_price' => $account->price,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->getKey(),
            'balance' => 0,
        ]);

        $account->refresh();
        $this->assertTrue(now()->gte($account->bought_at));
        $this->assertTrue(now()->lte($account->confirmed_at));
    }

    public function test_middleware_not_enough_balance()
    {
        $user = $this->factoryUser(inverse: true, user: User::factory()->state([
            'balance' => 10000,
        ])->create());
        $account = Account::factory()->state([
            'price' => 10001,
            'bought_at' => null,
        ])->create();
        $router = route('accounts.buy', ['account' => $account]);

        $this->actingAs($user)
            ->json('patch', $router)
            ->assertStatus(403);
    }

    public function test_middleware_bought_account()
    {
        $user = $this->factoryUser(inverse: true, user: User::factory()->state([
            'balance' => 10000,
        ])->create());
        $account = Account::factory()->state([
            'price' => 9999,
            'bought_at' => now(),
        ])->create();
        $router = route('accounts.buy', ['account' => $account]);

        $this->actingAs($user)
            ->json('patch', $router)
            ->assertStatus(403);
    }
}

<?php

namespace Tests\Feature\Account;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ResourceTest extends TestCase
{
    public function test_sensitiveInfos()
    {
        $user = $this->factoryUser(inverse: true);
        $account = Account::factory()->state([
            'cost' => 1000,
        ])
            ->for(User::factory(), 'creator')
            ->create();
        $router = route('accounts.show', ['account' => $account]);
        $data = [
            "_sensitive" => true,
        ];

        $this->actingAs($user)
            ->json('get', $router, $data)
            ->assertJson(fn (AssertableJson $j) => $j
                ->where('data.cost', null));

        $this->actingAs($account->creator)
            ->json('get', $router,  $data)
            ->assertJson(fn (AssertableJson $j) => $j
                ->where('data.cost', $account->cost));
    }
}

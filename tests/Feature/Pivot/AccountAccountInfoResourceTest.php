<?php

namespace Tests\Feature\Pivot;

use App\Models\Account;
use App\Models\AccountInfo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AccountAccountInfoResourceTest extends TestCase
{
    public function test_selling_account_case()
    {
        $creator = $this->factoryUser();
        $user = $this->factoryUser(inverse: true);
        $account = Account::factory()
            ->state([
                'bought_at' => null,
                'creator_id' => $creator->getKey(),
            ])
            ->hasAttached(AccountInfo::factory(), [
                'value' => 'thisIsPivotValue',
            ], 'infos')
            ->create();
        $router = route('accounts.show', ['account' => $account]);
        $data = [
            '_sensitive' => true,
            '_relationships' => ['infos']
        ];

        $this->actingAs($creator)
            ->json('get', $router, $data)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('data.infos.0.pivot.value', 'thisIsPivotValue')
            );

        $this->actingAs($user)
            ->json('get', $router, $data)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('data.infos.0.pivot.value', null)
            );
    }

    public function test_bought_confirming_account_case()
    {
        $buyer = $this->factoryUser();
        $creator = $this->factoryUser(inverse: true);
        $user = $this->factoryUser(inverse: true);
        $account = Account::factory()
            ->state([
                'bought_at' => now()->subMinute(),
                'confirmed_at' => now()->addHour(),
                'buyer_id' => $buyer->getKey(),
                'creator_id' => $creator->getKey(),
            ])
            ->hasAttached(AccountInfo::factory(), [
                'value' => 'thisIsPivotValue',
            ], 'infos')
            ->create();
        $router = route('accounts.show', ['account' => $account]);
        $data = [
            '_sensitive' => true,
            '_relationships' => ['infos']
        ];

        $this->actingAs($creator)
            ->json('get', $router, $data)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('data.infos.0.pivot.value', null)
            );

        $this->actingAs($user)
            ->json('get', $router, $data)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('data.infos.0.pivot.value', null)
            );

        $this->actingAs($creator)
            ->json('get', $router, $data)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('data.infos.0.pivot.value', null)
            );

        $this->actingAs($buyer)
            ->json('get', $router, $data)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('data.infos.0.pivot.value', 'thisIsPivotValue')
            );
    }

    public function test_bought_oke_account_case()
    {
        $buyer = $this->factoryUser();
        $creator = $this->factoryUser(inverse: true);
        $user = $this->factoryUser(inverse: true);
        $account = Account::factory()
            ->state([
                'bought_at' => now()->subMinute(),
                'confirmed_at' => now()->subMinute(),
                'buyer_id' => $buyer->getKey(),
                'creator_id' => $creator->getKey(),
            ])
            ->hasAttached(AccountInfo::factory(), [
                'value' => 'thisIsPivotValue',
            ], 'infos')
            ->create();
        $router = route('accounts.show', ['account' => $account]);
        $data = [
            '_sensitive' => true,
            '_relationships' => ['infos']
        ];

        $this->actingAs($creator)
            ->json('get', $router, $data)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('data.infos.0.pivot.value', null)
            );

        $this->actingAs($user)
            ->json('get', $router, $data)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('data.infos.0.pivot.value', null)
            );

        $this->actingAs($creator)
            ->json('get', $router, $data)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('data.infos.0.pivot.value', null)
            );

        $this->actingAs($buyer)
            ->json('get', $router, $data)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('data.infos.0.pivot.value', 'thisIsPivotValue')
            );
    }

    public function test_bought_not_oke_account_case()
    {
        $buyer = $this->factoryUser();
        $creator = $this->factoryUser(inverse: true);
        $user = $this->factoryUser(inverse: true);
        $account = Account::factory()
            ->state([
                'bought_at' => now()->subMinute(),
                'confirmed_at' => null,
                'buyer_id' => $buyer->getKey(),
                'creator_id' => $creator->getKey(),
            ])
            ->hasAttached(AccountInfo::factory(), [
                'value' => 'thisIsPivotValue',
            ], 'infos')
            ->create();
        $router = route('accounts.show', ['account' => $account]);
        $data = [
            '_sensitive' => true,
            '_relationships' => ['infos']
        ];

        $this->actingAs($creator)
            ->json('get', $router, $data)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('data.infos.0.pivot.value', null)
            );

        $this->actingAs($user)
            ->json('get', $router, $data)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('data.infos.0.pivot.value', null)
            );

        $this->actingAs($creator)
            ->json('get', $router, $data)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('data.infos.0.pivot.value', null)
            );

        $this->actingAs($buyer)
            ->json('get', $router, $data)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('data.infos.0.pivot.value', null)
            );
    }
}

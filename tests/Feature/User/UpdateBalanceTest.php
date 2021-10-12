<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Str;
use Tests\TestCase;

class UpdateBalanceTest extends TestCase
{
    public function test_controller()
    {
        $auth = $this->factoryUser(['update_user']);
        $user = User::factory()
            ->state([
                'balance' => 1000,
            ])
            ->create();
        $router = route('users.updateBalance', ['user' => $user]);
        $data1 = [
            'amount' => -1000,
            'description' => Str::random(),
        ];

        $this->actingAs($auth)
            ->json('patch', $router, $data1)
            ->assertStatus(200);

        $data2 = [
            'amount' => 5000,
            'description' => Str::random(),
        ];

        $this->actingAs($auth)
            ->json('patch', $router, $data2)
            ->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->getKey(),
            'balance' => 5000,
        ]);

        $this->assertEquals(2, $user->refresh()->logs()->count());
    }

    public function test_middleware_lack_update_user_permission_and_is_creator()
    {
        $user = $this->factoryUser(['update_user'], true);
        $router = route('users.updateBalance', ['user' => $user]);

        $this->actingAs($user)
            ->json('patch', $router)
            ->assertStatus(403);
    }
}

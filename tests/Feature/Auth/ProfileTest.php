<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    public function test_controller()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->json('get', route('profile'))
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->where('data.id', $user->getKey())
                    ->where('data.login', $user->login)
            );
    }
}

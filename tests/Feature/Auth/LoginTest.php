<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;
use Request;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_controller()
    {
        $user = User::factory()->create();

        $this->json('post', route('login'), [
            'login' => $user->login,
            'password' => 'password',
        ])
            ->assertStatus(200);

        $this->json('get', route('profile'))
            ->assertStatus(200);
    }

    public function test_controller_invalid_password()
    {
        $user = User::factory()->create();

        $this->json('post', route('login'), [
            'login' => $user->login,
            'password' => 'invalidPassword',
        ])
            ->assertStatus(400);

        $this->json('get', route('profile'))
            ->assertStatus(401);
    }
}

<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Session\SessionManager;
use Mockery\MockInterface;
use Request;
use Session;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    public function test_controller()
    {
        // $this->withSession([]);
        // Session::shouldReceive('invalidate')->once();

        $user = User::factory()->create();

        // $this->actingAs($user)
        //     ->json('post', route('logout'))
        //     ->assertStatus(200);

        // $this->json('get', route('profile'))
        //     ->assertStatus(401);
    }
}

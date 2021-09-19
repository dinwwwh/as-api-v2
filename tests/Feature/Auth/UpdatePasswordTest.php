<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Hash;

class UpdatePasswordTest extends TestCase
{
    public function test_controller()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->json('patch', route('password.update'), [
            'oldPassword'=> 'password',
            'newPassword' => 'newPassword'
        ])
        ->assertStatus(200);

        $this->assertTrue(Hash::check('newPassword', $user->refresh()->password));
    }

    public function test_controller_invalid_old_password(){
        $user = User::factory()->create();

        $this->actingAs($user)->json('patch', route('password.update'), [
            'oldPassword'=> 'invalidPassword',
            'newPassword' => 'newPassword'
        ])
        ->assertStatus(400);

        $this->assertFalse(Hash::check('newPassword', $user->refresh()->password));
    }

    public function test_middleware(){
        $this->json('patch', route('password.update'))
        ->assertStatus(401);
    }
}

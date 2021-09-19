<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Arr;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Storage;
use Str;
use Tests\TestCase;

class UpdateProfileTest extends TestCase
{
    public function test_controller()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $data = [
            'name' => Str::random(),
            'gender' => Arr::random(['male', 'female', 'other']),
            'avatar' => UploadedFile::fake()->image('photo1.jpg'),
        ];

        $this->actingAs($user)
            ->json('PATCH', route('profile.update'), $data)
            ->assertStatus(200);

        Storage::disk('public')->assertExists('avatars/' . $data['avatar']->hashName());
        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'gender' => $data['gender'],
            'avatar_path' => 'avatars/' . $data['avatar']->hashName(),
            'id' => $user->id,
        ]);
    }

    public function test_middleware()
    {
        $this->json('PATCH', route('profile.update'))
            ->assertStatus(401);
    }
}

<?php

namespace Tests\Feature\AccountType;

use App\Models\AccountType;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Str;
use Tests\TestCase;

class CreateTest extends TestCase
{
    public function test_controller()
    {
        $user = $this->factoryUser(['create_account_type']);
        $router = route('accountTypes.create');
        $data = [
            'name' => Str::random(),
            'description' => Str::random(),
            'tags' => Tag::factory()->count(5)->make()->toArray(),
            'users' => User::inRandomOrder()->limit(5)->get()->toArray(),
        ];

        $resData = $this->actingAs($user)
            ->json('post', $router, $data)
            ->assertStatus(201)
            ->getData()
            ->data;

        $this->assertDatabaseHas('account_types', [
            'id' => $resData->id,
            'name' => $data['name'],
            'description' => $data['description'],
        ]);

        $accountType = AccountType::find($resData->id);

        $this->assertEquals(count($data['users']), $accountType->users()->count());
        $this->assertEquals(count($data['tags']), $accountType->tags()->count());
        $this->assertEquals(1, $accountType->logs()->count());
    }

    public function test_middleware_lack_create_account_type()
    {
        $user = $this->factoryUser(['create_account_type'], true);
        $router = route('accountTypes.create');

        $this->actingAs($user)
            ->json('post', $router)
            ->assertStatus(403);
    }
}

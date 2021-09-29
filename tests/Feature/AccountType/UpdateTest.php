<?php

namespace Tests\Feature\AccountType;

use App\Models\AccountType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Str;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    public function test_controller()
    {
        $user = $this->factoryUser(['update_account_type']);
        $accountType = AccountType::factory()->state([
            'creator_id' => $user->getKey(),
        ])->create();
        $router = route('accountTypes.update', ['accountType' => $accountType]);
        $data = [
            'name' => Str::random(),
            'description' => Str::random(),
            'tags' => [
                [
                    'name' => Str::random(),
                    'description' => Str::random(),
                ],
                [
                    'name' => Str::random(),
                    'description' => Str::random(),
                ],
            ],
            'userIds' => User::inRandomOrder()->limit(5)->pluck('id')->toArray(),
        ];

        $this->actingAs($user)
            ->json('put', $router, $data)
            ->assertStatus(200);

        $this->assertDatabaseHas('account_types', [
            'id' => $accountType->getKey(),
            'name' => $data['name'],
            'description' => $data['description'],
        ]);

        $this->assertEquals(count($data['userIds']), $accountType->users()->count());
        $this->assertEquals(count($data['tags']), $accountType->tags()->count());
        $this->assertEquals(1, $accountType->logs()->count());
    }

    public function test_middleware_lack_update_account_type_as_creator_and_manager()
    {
        $user = $this->factoryUser(['update_account_type'], true);
        $accountType = AccountType::factory()->state([
            'creator_id' => $user->getKey(),
        ])->create();
        $router = route('accountTypes.update', ['accountType' => $accountType]);

        $this->actingAs($user)
            ->json('put', $router)
            ->assertStatus(403);
    }

    public function test_middleware_as_manager()
    {
        $user = $this->factoryUser(['update_account_type', 'manage_account_type']);
        $accountType = AccountType::factory()->create();
        $router = route('accountTypes.update', ['accountType' => $accountType]);

        $resStatus =  $this->actingAs($user)
            ->json('put', $router)
            ->status();

        $this->assertNotEquals(403, $resStatus);
    }
}

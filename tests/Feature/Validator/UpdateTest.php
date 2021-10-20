<?php

namespace Tests\Feature\Validator;

use App\Models\User;
use App\Models\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Str;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    public function test_controller()
    {
        $user = $this->factoryUser(['update_validator']);
        $validator = Validator::factory()->state([
            'creator_id' => $user->getKey(),
        ])->create();
        $router = route('validators.update', ['validator' => $validator]);
        $data = [
            'name' => Str::random(32),
            'description' => $this->faker->sentence(12),
            'approverDescription' => $this->faker->sentence(12),
            'readableFields' => ['tai khoan', 'mat khau'],
            'updatableFields' => ['mat khau'],
            'users' => User::factory()->count(rand(2, 5))->create()->toArray(),
        ];

        $this->actingAs($user)
            ->json('put', $router, $data)
            ->assertStatus(200);

        $this->assertDatabaseHas('validators', [
            'id' => $validator->getKey(),
            'name' => $data['name'],
            'slug' => $data['name'],
            'description' => $data['description'],
            'approver_description' => $data['approverDescription'],
            'readable_fields' => $this->castToJson($data['readableFields']),
            'updatable_fields' => $this->castToJson($data['updatableFields']),
        ]);

        $this->assertEquals(count($data['users']), $validator->users()->count());
    }

    public function test_middleware_lack_update_validator_permission()
    {
        $user = $this->factoryUser(['update_validator'], true);
        $validator = Validator::factory()->state([
            'creator_id' => $user->getKey(),
        ])->create();
        $router = route('validators.update', ['validator' => $validator]);

        $this->actingAs($user)
            ->json('put', $router)
            ->assertStatus(403);
    }

    public function test_middleware_has_manage_and_update_validator_permission()
    {
        $user = $this->factoryUser(['update_validator', 'manage_validator']);
        $validator = Validator::factory()->create();
        $router = route('validators.update', ['validator' => $validator]);

        $status = $this->actingAs($user)
            ->json('put', $router)
            ->status();

        $this->assertTrue(in_array($status, [200, 422]));
    }

    public function test_middleware_lack_manage_permission_and_is_not_creator()
    {
        $user = $this->factoryUser(['manage_validator'], true);
        $validator = Validator::factory()->create();
        $router = route('validators.update', ['validator' => $validator]);

        $this->actingAs($user)
            ->json('put', $router)
            ->assertStatus(403);
    }
}

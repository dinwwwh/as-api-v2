<?php

namespace Tests\Feature\Validator;

use App\Models\User;
use App\Models\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Str;
use Tests\TestCase;

class CreateTest extends TestCase
{
    public function test_controller()
    {
        $user = $this->factoryUser(['create_validator']);
        $router = route('validators.create');
        $data = [
            'name' => Str::random(32),
            'description' => $this->faker->sentence(12),
            'approverDescription' => $this->faker->sentence(12),
            'readableFields' => ['tai khoan', 'mat khau'],
            'updatableFields' => ['mat khau'],
            'users' => User::factory()->count(rand(2, 5))->create()->toArray(),
        ];

        $id = $this->actingAs($user)
            ->json('post', $router, $data)
            ->assertStatus(201)
            ->getData()
            ->data
            ->id;

        $validator = Validator::find($id);

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

    public function test_middleware_lack_create_validator_permission()
    {
        $user = $this->factoryUser(['create_validator'], true);
        $router = route('validators.create');

        $this->actingAs($user)
            ->json('post', $router)
            ->assertStatus(403);
    }
}

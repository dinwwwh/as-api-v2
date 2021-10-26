<?php

namespace Tests\Feature\Validation;

use App\Models\Validation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StartApprovingTest extends TestCase
{
    public function test_controller()
    {
        $user = $this->factoryUser();
        $validation = Validation::factory()->create();
        $validation->validatorable->validator->users()->attach($user);
        $router = route('validations.startApproving', ['validation' => $validation]);

        $this->actingAs($user)
            ->json('patch', $router)
            ->assertStatus(200);

        $this->assertDatabaseHas('validations', [
            'id' => $validation->getKey(),
            'approver_id' => $user->getKey(),
            'is_approving' => true,
        ]);
    }

    public function test_middleware_lack_user_in_validator()
    {
        $user = $this->factoryUser();
        $validation = Validation::factory()->create();
        $router = route('validations.startApproving', ['validation' => $validation]);

        $this->actingAs($user)
            ->json('patch', $router)
            ->assertStatus(403);
    }

    public function test_middleware_invalid_validation()
    {
        $user = $this->factoryUser();
        $validation = Validation::factory()->state([
            'approver_id' => $user->getKey(),
        ])->create();
        $validation->validatorable->validator->users()->attach($user);
        $router = route('validations.startApproving', ['validation' => $validation]);

        $this->actingAs($user)
            ->json('patch', $router)
            ->assertStatus(403);
    }
}

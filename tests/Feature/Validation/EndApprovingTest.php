<?php

namespace Tests\Feature\Validation;

use App\Models\Account;
use App\Models\User;
use App\Models\Validation;
use App\Models\Validator;
use App\Models\Validatorable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EndApprovingTest extends TestCase
{
    public function test_controller()
    {
        $user = $this->factoryUser();
        $validation = Validation::factory()->state([
            'is_approving' => true,
            'approver_id' => $user->getKey(),
            'validationable_id' => Account::factory()->create()->getKey(),
            'validationable_type' => (new Account)->getMorphClass(),
            'validatorable_id' => Validatorable::factory()->state([
                'validator_id' => Validator::factory()->state([
                    'updatable_fields' => [],
                ]),
            ]),
        ])->create();
        $validation->validatorable->validator->users()->attach($user);
        $router = route('validations.endApproving', ['validation' => $validation]);
        $data = [
            'isError' => true,
            'description' => $this->faker->sentence(),
            'updatedValues' => [],
        ];

        $this->actingAs($user)
            ->json('patch', $router, $data)
            ->assertStatus(200);

        $this->assertDatabaseHas('validations', [
            'id' => $validation->getKey(),
            'approver_id' => $user->getKey(),
            'is_approving' => false,
            'is_error' => $data['isError'],
            'description' => $data['description'],
            'updated_values' => $this->castToJson($data['updatedValues']),
        ]);
    }

    public function test_middleware_lack_user_in_validator()
    {
        $user = $this->factoryUser();
        $validation = Validation::factory()->state([
            'is_approving' => true,
            'approver_id' => $user->getKey(),
        ])->create();
        $router = route('validations.endApproving', ['validation' => $validation]);

        $this->actingAs($user)
            ->json('patch', $router)
            ->assertStatus(403);
    }

    public function test_middleware_invalid_validation_1()
    {
        $user = $this->factoryUser();
        $validation = Validation::factory()->state([
            'is_approving' => false,
            'approver_id' => $user->getKey(),
        ])->create();
        $validation->validatorable->validator->users()->attach($user);
        $router = route('validations.endApproving', ['validation' => $validation]);

        $this->actingAs($user)
            ->json('patch', $router)
            ->assertStatus(403);
    }

    public function test_middleware_invalid_validation_2()
    {
        $user = $this->factoryUser();
        $validation = Validation::factory()->state([
            'is_approving' => true,
            'approver_id' => User::factory()->create()->getKey(),
        ])->create();
        $validation->validatorable->validator->users()->attach($user);
        $router = route('validations.endApproving', ['validation' => $validation]);

        $this->actingAs($user)
            ->json('patch', $router)
            ->assertStatus(403);
    }
}

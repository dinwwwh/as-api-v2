<?php

namespace Tests\Unit\Validation;

use App\Models\Account;
use App\Models\User;
use App\Models\Validation;
use App\Models\Validator;
use Tests\TestCase;

class ModelTest extends TestCase
{
    public function test_touch_validationable_per_event()
    {

        $user = $this->factoryUser();

        sleep(1);
        $validation = Validation::factory()
            ->state([
                'validationable_id' => $user->getKey(),
                'validationable_type' => $user->getMorphClass(),
            ])
            ->create();

        $this->assertTrue($user->updated_at->lt($user->fresh()->updated_at));

        sleep(1);
        $validation->update([
            'description' => 'test',
        ]);

        $this->assertTrue($user->updated_at->lt($user->fresh()->updated_at));

        sleep(1);
        $validation->delete();

        $this->assertTrue($user->updated_at->lt($user->fresh()->updated_at));
    }

    public function test_create_next_validation_when_success()
    {
        $account = Account::factory()->create();
        $accountType = $account->accountType;
        $validator1 = Validator::factory()->create();
        $validator2 = Validator::factory()->create();

        $validatorable1 = $accountType->validatorables()->create([
            'validator_id' => $validator1->getKey(),
            'order' => null,
            'mapped_readable_fields' => [],
            'mapped_updatable_fields' => [],
        ]);
        $validatorable2 = $accountType->validatorables()->create([
            'validator_id' => $validator2->getKey(),
            'mapped_readable_fields' => [],
            'mapped_updatable_fields' => [],
            'order' => 2,
        ]);
        $validatorable3 = $accountType->validatorables()->create([
            'validator_id' => $validator2->getKey(),
            'order' => 2,
            'type' => 2,
            'mapped_readable_fields' => [],
            'mapped_updatable_fields' => [],
        ]);

        $account->validations()->create([
            'validatorable_id' => $validatorable1->getKey(),
        ]);

        $this->assertEquals(1, $account->validations()->count());

        $account->validations()->first()->update([
            'approver_id' => User::factory()->create()->getKey(),
        ]);
        $account->validations()->first()->update([
            'description' => 'expected not create any validations'
        ]);
        $account->validations()->first()->update([
            'description' => 'TEST AGAIN FOR CERTAINLY'
        ]);

        $this->assertEquals(2, $account->validations()->count());
        $this->assertEquals(
            $validatorable2->getKey(),
            $account->validations()->latest()->first()->validatorable->getKey()
        );

        $account->validations()->latest()->first()->update([
            'approver_id' => User::factory()->create()->getKey(),
        ]);

        $this->assertEquals(2, $account->validations()->count());
    }

    public function test_create_next_validation_when_error()
    {
        $account = Account::factory()->create();
        $accountType = $account->accountType;
        $validator1 = Validator::factory()->create();
        $validator2 = Validator::factory()->create();

        $validatorable1 = $accountType->validatorables()->create([
            'validator_id' => $validator1->getKey(),
            'order' => null,
            'mapped_readable_fields' => [],
            'mapped_updatable_fields' => [],
        ]);
        $validatorable2 = $accountType->validatorables()->create([
            'validator_id' => $validator2->getKey(),
            'mapped_readable_fields' => [],
            'mapped_updatable_fields' => [],
            'order' => 2,
        ]);
        $validatorable3 = $accountType->validatorables()->create([
            'validator_id' => $validator2->getKey(),
            'order' => 2,
            'type' => 2,
            'mapped_readable_fields' => [],
            'mapped_updatable_fields' => [],
        ]);

        $account->validations()->create([
            'validatorable_id' => $accountType->validatorables()->first()->getKey(),
        ]);

        $this->assertEquals(1, $account->validations()->count());

        $account->validations()->first()->update([
            'approver_id' => User::factory()->create()->getKey(),
            'is_approving' => true,
        ]);

        $this->assertEquals(1, $account->validations()->count());

        $account->validations()->first()->update([
            'approver_id' => User::factory()->create()->getKey(),
            'is_error' => true,
        ]);

        $this->assertEquals(1, $account->validations()->count());
    }
}

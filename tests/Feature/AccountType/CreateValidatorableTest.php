<?php

namespace Tests\Feature\AccountType;

use App\Models\AccountInfo;
use App\Models\AccountType;
use App\Models\Validator;
use App\Models\Validatorable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateValidatorableTest extends TestCase
{
    public function test_controller()
    {
        $user = $this->factoryUser(['update_account_type', 'manage_account_type']);
        $accountType = AccountType::factory()->create();
        $accountInfo = AccountInfo::factory()->state(['account_type_id' => $accountType->getKey()])->create();
        $validator = Validator::factory()->state([
            'readable_fields' => ['field_1'],
            'updatable_fields' => ['field_2'],
        ])->create();
        $router = route('accountTypes.createValidatorable', compact('accountType', 'validator'));
        $data = [
            'type' => Validatorable::CREATED_TYPE,
            'mappedReadableFields' => [
                'field_1' => $accountInfo->getKey(),
            ],
            'mappedUpdatableFields' => [
                'field_2' => $accountInfo->getKey(),
            ],
        ];

        $this->actingAs($user)
            ->json('post', $router, $data)
            ->assertStatus(200);

        $this->assertEquals(1, $accountType->validatorables()->count());
    }

    public function test_middleware_lack_update_account_type_as_creator_and_manager()
    {
        $user = $this->factoryUser(['update_account_type'], true);
        $accountType = AccountType::factory()->state([
            'creator_id' => $user->getKey(),
        ])->create();
        $validator = Validator::factory()->create();
        $router = route('accountTypes.createValidatorable', compact('accountType', 'validator'));

        $this->actingAs($user)
            ->json('post', $router)
            ->assertStatus(403);
    }

    public function test_middleware_as_creator()
    {
        $user = $this->factoryUser(['update_account_type']);
        $accountType = AccountType::factory()->state([
            'creator_id' => $user->getKey(),
        ])->create();
        $validator = Validator::factory()->create();
        $router = route('accountTypes.createValidatorable', compact('accountType', 'validator'));

        $resStatus =  $this->actingAs($user)
            ->json('post', $router)
            ->status();

        $this->assertNotEquals(403, $resStatus);
    }
}

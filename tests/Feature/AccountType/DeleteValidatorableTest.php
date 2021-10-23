<?php

namespace Tests\Feature\AccountType;

use App\Models\AccountType;
use App\Models\Validatorable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteValidatorableTest extends TestCase
{
    public function test_controller()
    {
        $user = $this->factoryUser(['update_account_type', 'manage_account_type']);
        $accountType = AccountType::factory()->create();
        $validatorable = Validatorable::factory()->state([
            'parent_id' => $accountType->getKey(),
            'parent_type' => $accountType->getMorphClass(),
        ])->create();
        $router = route('accountTypes.deleteValidatorable', compact('accountType', 'validatorable'));

        $this->actingAs($user)
            ->json('delete', $router)
            ->assertStatus(200);

        $this->assertEquals(0, $accountType->validatorables()->count());
    }

    public function test_middleware_lack_update_account_type_as_creator_and_manager()
    {
        $user = $this->factoryUser(['update_account_type'], true);
        $accountType = AccountType::factory()->create();
        $validatorable = Validatorable::factory()->state([
            'parent_id' => $accountType->getKey(),
            'parent_type' => $accountType->getMorphClass(),
        ])->create();
        $router = route('accountTypes.deleteValidatorable', compact('accountType', 'validatorable'));

        $this->actingAs($user)
            ->json('delete', $router)
            ->assertStatus(403);
    }

    public function test_middleware_as_creator()
    {
        $user = $this->factoryUser(['update_account_type']);
        $accountType = AccountType::factory()->state([
            'creator_id' => $user->getKey(),
        ])->create();
        $validatorable = Validatorable::factory()->state([
            'parent_id' => $accountType->getKey(),
            'parent_type' => $accountType->getMorphClass(),
        ])->create();
        $router = route('accountTypes.deleteValidatorable', compact('accountType', 'validatorable'));

        $this->actingAs($user)
            ->json('delete', $router)
            ->assertStatus(200);
    }

    public function test_middleware_invalid_validatorable()
    {
        $user = $this->factoryUser(['update_account_type']);
        $accountType = AccountType::factory()->state([
            'creator_id' => $user->getKey(),
        ])->create();
        $validatorable = Validatorable::factory()->create();
        $router = route('accountTypes.deleteValidatorable', compact('accountType', 'validatorable'));

        $this->actingAs($user)
            ->json('delete', $router)
            ->assertStatus(403);
    }
}

<?php

namespace Tests\Unit\Validatorable;

use App\Models\Validatorable;
use App\Models\Validation;
use Illuminate\Database\Eloquent\Builder;
use Tests\TestCase;


class ModelTest extends TestCase
{
    public function test_updating_event()
    {
        $validatorable = Validatorable::factory()
            ->has(Validation::factory()->count(5))
            ->create();
        $undeleteValidation = Validation::factory()->state([
            'validatorable_id' => $validatorable->getKey(),
            'is_approving' => false,
            'approver_id' => $this->factoryUser()->getKey(),
        ])->create();

        $this->assertEquals(6, $validatorable->validations()->count());

        $validatorable->delete();

        $this->assertEquals(0, $validatorable->validations()->count());
        $this->assertEquals(null, $undeleteValidation->refresh()->validatorable_id);
    }
}

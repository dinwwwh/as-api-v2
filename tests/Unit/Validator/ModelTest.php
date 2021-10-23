<?php

namespace Tests\Unit\Validator;

use App\Models\AccountType;
use App\Models\Validation;
use App\Models\Validator;
use App\Models\Validatorable;
use Tests\TestCase;

class ModelTest extends TestCase
{
    public function test_updating_event()
    {
        $validator = Validator::factory()
            ->has(Validatorable::factory()->count(5))
            ->create();

        $validator->update([
            'name' => $this->faker->name()
        ]);

        $this->assertEquals(5, $validator->validatorables()->count());

        $validator->update([
            'updatable_fields' => []
        ]);

        $this->assertEquals(0, $validator->validatorables()->count());
    }

    public function test_deleting_event()
    {
        $validator = Validator::factory()
            ->has(Validatorable::factory()->count(5))
            ->create();

        $this->assertEquals(5, $validator->validatorables()->count());

        $validator->delete();

        $this->assertEquals(0, $validator->validatorables()->count());
    }
}

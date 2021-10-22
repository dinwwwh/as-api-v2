<?php

namespace Tests\Unit\Validator;

use App\Models\AccountType;
use App\Models\Validation;
use App\Models\Validator;
use Tests\TestCase;

class ModelTest extends TestCase
{
    public function test_updating_event()
    {
        $validator = Validator::factory()
            ->hasAttached(AccountType::factory()->count(5), [
                'mapped_readable_fields' => json_encode([]),
                'mapped_updatable_fields' => json_encode([]),
            ])
            ->has(Validation::factory()->count(4))
            ->create();

        $validator->update([
            'name' => $this->faker->name()
        ]);

        $this->assertEquals(5, $validator->accountTypes()->count());
        $this->assertEquals(4, $validator->validations()->count());

        $validator->update([
            'updatable_fields' => []
        ]);

        $this->assertEquals(0, $validator->accountTypes()->count());
        $this->assertEquals(0, $validator->validations()->count());
    }
}

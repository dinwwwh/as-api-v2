<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Validation;
use App\Models\Validatorable;
use Illuminate\Database\Eloquent\Factories\Factory;

class ValidationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Validation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'validatorable_id' => Validatorable::factory(),
            'validationable_id' => Account::factory(),
            'validationable_type' => (new Account)->getMorphClass(),
            'is_approving' => false,
            'is_error' => false,
            'approver_id' => null,
            'updated_values' => null,
            'description' => $this->faker->sentence(12),
        ];
    }
}

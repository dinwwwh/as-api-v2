<?php

namespace Database\Factories;

use App\Models\AccountInfo;
use App\Models\AccountType;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountInfoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AccountInfo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(12),
            'account_type_id' => AccountType::factory(),
        ];
    }
}

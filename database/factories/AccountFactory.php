<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->sentence(12),
            'cost' => rand(1000, 9999),
            'price' => rand(10000, 99999),
            'tax' => rand(1000, 9999),
            'account_type_id' => AccountType::factory(),
        ];
    }

    /**
     * State model to buying status.
     *
     */
    public function buying()
    {
        return $this->state([
            'confirmed_at' => now()->addHour(),
            'buyer_id' => User::factory(),
            'bought_at' => now(),
        ]);
    }

    /**
     * State model to approvable status.
     *
     */
    public function approvable()
    {
        return $this->buying()->state([
            'confirmed_at' => null,
        ]);
    }
}

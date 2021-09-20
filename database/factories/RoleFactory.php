<?php

namespace Database\Factories;

use App\Models\Role;
use Arr;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'key' => Str::random(),
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(),
            'color' => Arr::random(['red', 'blue', 'yellow', 'orange', 'green']),
        ];
    }
}

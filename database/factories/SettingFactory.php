<?php

namespace Database\Factories;

use App\Models\Setting;
use Arr;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

class SettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'key' => Str::random(36),
            'value' => Str::random(),
            'assigned_config_key' => Arr::random([null, Str::random()]),
            'rules' => null,
            'structure_description' => $this->faker->sentence(),
            'description' => $this->faker->sentence(),
            'public' => Arr::random([true, false]),
        ];
    }
}

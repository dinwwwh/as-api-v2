<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\User;
use Arr;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

class FileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'path' => Str::random() . '.' . Arr::random(File::IMAGE_EXTENSIONS),
            'description' => $this->faker->sentence(12),
            'order' => rand(1, 99),
            'filable_id' => User::factory(),
            'filable_type' => (new User)->getMorphClass(),
        ];
    }
}

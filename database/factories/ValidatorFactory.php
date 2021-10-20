<?php

namespace Database\Factories;

use App\Models\Validator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

class ValidatorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Validator::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        try {
            $name  = $this->faker->name();
            for ($i = 0; Validator::where('slug', Str::slug($name))->first(); $i++) {
                $name  = $this->faker->name();

                if ($i == 100) break;
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        return [
            'name' => $name ?? $this->faker->name(),
            'description' => $this->faker->sentence(12),
            'approver_description' => $this->faker->sentence(12),
            'readable_fields' => ['filed 1'],
            'updatable_fields' => ['filed 2'],
            'callback' => null,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\RechargedCard;
use Arr;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

class RechargedCardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RechargedCard::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'serial' => Str::random(),
            'code' => Str::random(),
            'telco' => 'viettel',
            'face_value' => Arr::random([10000, 20000, 50000, 100000, 200000, 500000]),
            'real_face_value' => null,
            'received_value' => null,
            'description' => $this->faker->sentence(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Arr;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->name();
        $gender = Arr::random(['male', 'female', 'other']);
        $sprite = $gender == 'other' ? 'human' : $gender;

        // Need try/catch since default unit tests don't connect to database
        try {
            do {
                $email = $this->faker->unique()->safeEmail();
            } while (User::where('email', $email)->first());
        } catch (\Throwable $th) {
            //throw $th;
            $email = $this->faker->unique()->safeEmail();
        }

        return [
            'name' => $name,
            'balance' => rand(10000, 500000),
            'login' => Str::random(),
            'gender' => $gender,
            'avatar_path' => "https://avatars.dicebear.com/api/$sprite/$name.svg",
            'email' => $email,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}

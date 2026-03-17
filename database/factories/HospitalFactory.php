<?php

namespace Database\Factories;

use App\Models\Hospital;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hospital>
 */
class HospitalFactory extends Factory
{
    protected $model = Hospital::class;
    public function definition(): array
    {
        return [
            'name' => fake()->company() . " Hospital",
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'is_active' => fake()->boolean(80),
            'is_verified' => fake()->boolean(80),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Foods>
 */
class FoodsFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'name_hu' => $this->faker->word(),
            'fat' => $this->faker->numberBetween(1, 100),
            'carb' => $this->faker->numberBetween(1, 100),
            'protein' => $this->faker->numberBetween(1, 100),
            'calorie' => $this->faker->numberBetween(1, 900),
            'unit' => 'g',
            'amount' => 100,
        ];
    }
}

<?php

namespace Database\Factories;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProfile>
 */
class UserProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'gender' => fake()->randomElement(['male', 'female']),
            'date_of_birth' => fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'height_cm' => fake()->numberBetween(150, 200),
            'weight_kg' => fake()->numberBetween(50, 120),
            'activity_level' => fake()->randomElement(['sedentary', 'light', 'moderate', 'active', 'very_active']),
            'weight_goal' => 'maintain',
            'target_weight_kg' => null,
            'goal_period_weeks' => 4,
        ];
    }

    public function losingWeight(): static
    {
        return $this->state(fn (array $attributes) => [
            'weight_goal' => 'lose',
            'target_weight_kg' => $attributes['weight_kg'] - 10,
            'goal_period_weeks' => 12,
        ]);
    }
}

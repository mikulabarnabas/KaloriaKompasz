<?php

namespace App\Observers;

use App\Models\UserProfile;

class UserProfileObserver
{
    public function creating(UserProfile $profile): void
    {
        $this->calculateMacros($profile);
    }

    public function updating(UserProfile $profile): void
    {
        $this->calculateMacros($profile);
    }

    private function calculateMacros(UserProfile $profile): void
    {
        // Ensure required data exists
        if (!$profile->weight_kg || !$profile->height_cm || !$profile->date_of_birth) {
            return;
        }

        $weight = $profile->weight_kg;
        $height = $profile->height_cm;
        $age = now()->diffInYears($profile->date_of_birth);
        $gender = $profile->gender;

        // BMR - Mifflin-St Jeor Equation
        $bmr = ($gender === 'male')
            ? 10 * $weight + 6.25 * $height - 5 * $age + 5
            : 10 * $weight + 6.25 * $height - 5 * $age - 161;

        // Activity multiplier
        $activityMultiplier = match($profile->activity_level) {
            'sedentary' => 1.2,
            'light' => 1.375,
            'moderate' => 1.55,
            'active' => 1.725,
            'very_active' => 1.9,
            default => 1.2,
        };

        $calories = $bmr * $activityMultiplier;

        // Adjust calories for weight goal
        if (in_array($profile->weight_goal, ['lose', 'gain']) && $profile->target_weight_kg && $profile->goal_period_weeks) {
            $weeks = $profile->goal_period_weeks ?: 1;

            if ($profile->weight_goal === 'lose') {
                $totalLossKg = $weight - $profile->target_weight_kg;
                $weeklyCalories = $totalLossKg * 7700 / $weeks;
                $calories -= $weeklyCalories / 7;
            } elseif ($profile->weight_goal === 'gain') {
                $totalGainKg = $profile->target_weight_kg - $weight;
                $weeklyCalories = $totalGainKg * 7700 / $weeks;
                $calories += $weeklyCalories / 7;
            }
        }

        // Macros split: 30% protein, 25% fat, 45% carbs
        $profile->calories_per_day = round($calories, 1);
        $profile->protein_per_day = round($calories * 0.3 / 4, 1);
        $profile->fat_per_day = round($calories * 0.25 / 9, 1);
        $profile->carbs_per_day = round($calories * 0.45 / 4, 1);
    }
}

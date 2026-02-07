<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'gender' => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date_format:Y-m-d|before:today',
            'height_cm' => 'nullable|integer|min:0|max:300',
            'weight_kg' => 'nullable|numeric|min:0|max:500',

            'activity_level' => 'nullable|string|in:sedentary,light,moderate,active,very_active',

            'weight_goal' => 'nullable|string|in:maintain,lose,gain',
            'target_weight_kg' => 'nullable|numeric|min:0|max:500|required_if:weight_goal,lose,gain',
            'goal_period_weeks' => 'nullable|integer|min:1|max:52|required_if:weight_goal,lose,gain',
        ];
    }
}

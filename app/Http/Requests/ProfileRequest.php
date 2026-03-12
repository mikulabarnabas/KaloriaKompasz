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
            'gender' => 'required|string|max:50',
            'date_of_birth' => 'required|date_format:Y-m-d|before:today',
            'height_cm' => 'required|integer|min:50|max:300',
            'weight_kg' => 'required|numeric|min:20|max:500',
            'activity_level' => 'required|string|in:sedentary,light,moderate,active,very_active',
            'weight_goal' => 'required|string|in:maintain,lose,gain',

            'target_weight_kg' => [
                'required_if:weight_goal,lose,gain',
                'nullable',
                'numeric',
                'min:20',
                'max:500'
            ],

            'goal_period_weeks' => [
                'required_if:weight_goal,lose,gain',
                'nullable',
                'integer',
                'min:1',
                'max:52'
            ],
        ];
    }
}

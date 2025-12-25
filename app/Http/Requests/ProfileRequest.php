<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'gender' => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date_format:Y-m-d|before:today',
            'height_cm' => 'nullable|integer|min:0|max:300',
            'weight_kg' => 'nullable|numeric|min:0|max:500',
            'activity_level' => 'nullable|string|max:50',
            'diet' => 'nullable|string|max:50',
        ];
    }
}

<?php

namespace App\Http\Requests\Api\Personnel;

use Illuminate\Foundation\Http\FormRequest;

class UpdateErpPersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'NAME' => 'sometimes|required|string|max:200',
            'FIRST_NAME' => 'nullable|string|max:100',
            'LAST_NAME' => 'nullable|string|max:100',
            'GENDER' => 'nullable|string|in:Male,Female,Other',
            'BIRTH_DATE_TIME' => 'nullable|date',
            'NATIONALITY' => 'nullable|string|max:100',
            'EMAIL' => 'nullable|email|max:200',
            'CATEGORY' => 'nullable|string|max:100',
            'ETHNICITY' => 'nullable|string|max:100',
            'MARITAL_STATUS' => 'nullable|string|max:50',
            'SPECIAL_NEEDS' => 'nullable|string|max:500',
        ];
    }
}
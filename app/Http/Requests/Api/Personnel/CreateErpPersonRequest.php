<?php

namespace App\Http\Requests\Api\Personnel;

use Illuminate\Foundation\Http\FormRequest;

class CreateErpPersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'MRID' => 'required|string|max:100|unique:ERP_PERSON,MRID',
            'NAME' => 'required|string|max:200',
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

    public function messages(): array
    {
        return [
            'MRID.required' => 'MRID is required',
            'MRID.unique' => 'This MRID already exists',
            'NAME.required' => 'Name is required',
            'GENDER.in' => 'Gender must be Male, Female, or Other',
            'BIRTH_DATE_TIME.date' => 'Birth date must be a valid date',
            'EMAIL.email' => 'Email must be a valid email address',
        ];
    }
}
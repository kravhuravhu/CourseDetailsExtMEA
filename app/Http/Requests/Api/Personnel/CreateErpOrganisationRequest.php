<?php

namespace App\Http\Requests\Api\Organisation;

use Illuminate\Foundation\Http\FormRequest;

class CreateErpOrganisationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'MRID' => 'required|string|max:100|unique:ERP_ORGANISATION,MRID',
            'NAME' => 'required|string|max:200',
            'CATEGORY' => 'nullable|string|max:100',
            'CODE' => 'nullable|string|max:100',
            'COMPANY_REGISTRATION_NO' => 'nullable|string|max:100',
            'VALUE_ADDED_TAX_ID' => 'nullable|string|max:100',
            'IS_COST_CENTER' => 'nullable|boolean',
            'IS_PROFIT_CENTER' => 'nullable|boolean',
            'BEE_RATING' => 'nullable|string|max:50',
        ];
    }
}
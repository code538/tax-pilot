<?php

namespace App\Http\Requests\Api\V1\FilingSubject;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFilingSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'company_number' => [
                'nullable',
                'string',
                'max:50',
            ],

            'company_type' => [
                'nullable',
                'string',
                'max:100',
            ],

            'utr_number' => [
                'nullable',
                'string',
                'max:50',
            ],

            'vat_number' => [
                'nullable',
                'string',
                'max:50',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'address_line_1' => [
                'nullable',
                'string',
                'max:255',
            ],

            'address_line_2' => [
                'nullable',
                'string',
                'max:255',
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'county' => [
                'nullable',
                'string',
                'max:100',
            ],

            'postcode' => [
                'nullable',
                'string',
                'max:20',
            ],

            'country' => [
                'nullable',
                'string',
                'max:100',
            ],

            'companies_house_status' => [
                'nullable',
                'string',
                'max:100',
            ],

            'incorporation_date' => [
                'nullable',
                'date',
            ],

            'dissolution_date' => [
                'nullable',
                'date',
                'after_or_equal:incorporation_date',
            ],

            'status' => [
                'nullable',
                'string',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}
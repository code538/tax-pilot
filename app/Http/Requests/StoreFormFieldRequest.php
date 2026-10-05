<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFormFieldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sectionId = $this->route('formSectionId');

        return [
            'field_key' => [
                'required',
                'string',
                'max:100',
                'alpha_dash',
                Rule::unique('form_fields', 'field_key')
                    ->where('form_section_id', $sectionId),
            ],

            'label' => ['required', 'string', 'max:200'],

            'field_type' => [
                'required',
                Rule::in([
                    'text',
                    'textarea',
                    'number',
                    'currency',
                    'date',
                    'email',
                    'select',
                    'radio',
                    'checkbox',
                    'boolean',
                    'file',
                ]),
            ],

            'placeholder' => ['nullable', 'string', 'max:255'],
            'help_text' => ['nullable', 'string'],
            'is_required' => ['sometimes', 'boolean'],
            'options' => ['nullable', 'array'],
            'options.*' => ['array'],
            'validation_rules' => ['nullable', 'array'],
            'external_path' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
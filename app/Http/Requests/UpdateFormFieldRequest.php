<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFormFieldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sectionId = $this->route('formSectionId');
        $fieldId = $this->route('formFieldId');

        return [
            'field_key' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                'alpha_dash',
                Rule::unique('form_fields', 'field_key')
                    ->where('form_section_id', $sectionId)
                    ->ignore($fieldId),
            ],

            'label' => ['sometimes', 'required', 'string', 'max:200'],

            'field_type' => [
                'sometimes',
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

            'placeholder' => ['sometimes', 'nullable', 'string', 'max:255'],
            'help_text' => ['sometimes', 'nullable', 'string'],
            'is_required' => ['sometimes', 'boolean'],
            'options' => ['sometimes', 'nullable', 'array'],
            'options.*' => ['array'],
            'validation_rules' => ['sometimes', 'nullable', 'array'],
            'external_path' => ['sometimes', 'nullable', 'string', 'max:255'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFormSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $formVersionId = $this->route('formVersionId');
        $formSectionId = $this->route('formSectionId');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:150'],

            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                'alpha_dash',
                Rule::unique('form_sections', 'slug')
                    ->where('form_version_id', $formVersionId)
                    ->ignore($formSectionId),
            ],

            'description' => ['sometimes', 'nullable', 'string'],

            'sort_order' => [
                'sometimes',
                'integer',
                'min:0',
            ],

            'is_repeatable' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}
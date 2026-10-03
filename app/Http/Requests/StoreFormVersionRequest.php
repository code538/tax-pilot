<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'form_definition_id' => [
                'required',
                'integer',
                'exists:form_definitions,id',
            ],
        ];
    }
}
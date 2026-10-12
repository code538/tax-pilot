<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page_name' => ['required', 'string', 'max:255'],
            'question'  => ['required', 'string'],
            'answer'    => ['required', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}

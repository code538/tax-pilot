<?php

namespace App\Http\Requests\Api\V1\Organisation;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'slug' => [
                'required',
                'string',
                'max:100',
                'alpha_dash',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'permission_ids' => [
                'nullable',
                'array',
            ],

            'permission_ids.*' => [
                'integer',
                'exists:permissions,id',
            ],
        ];
    }
}
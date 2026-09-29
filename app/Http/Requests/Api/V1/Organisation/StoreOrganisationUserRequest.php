<?php

namespace App\Http\Requests\Api\V1\Organisation;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganisationUserRequest extends FormRequest
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
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'role_id' => [
                'nullable',
                'integer',
                'exists:roles,id',
            ],
        ];
    }
}
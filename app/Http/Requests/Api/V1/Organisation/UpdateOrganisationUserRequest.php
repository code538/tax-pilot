<?php

namespace App\Http\Requests\Api\V1\Organisation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganisationUserRequest extends FormRequest
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

            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
            ],

            'status' => [
                'sometimes',
                'required',
                'in:active,inactive,suspended',
            ],

            'role_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:roles,id',
            ],
        ];
    }
}
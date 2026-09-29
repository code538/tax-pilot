<?php

namespace App\Http\Requests\Api\V1\Organisation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrganisationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $organisationId = $this->route('organisation')?->id
            ?? $this->route('organisation');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('organisations', 'slug')
                    ->ignore($organisationId),
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

            'website' => [
                'nullable',
                'url',
                'max:255',
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

            'type' => [
                'nullable',
                'string',
                'max:50',
            ],

            'status' => [
                'nullable',
                'in:active,inactive,suspended',
            ],

            'timezone' => [
                'nullable',
                'string',
                'max:100',
            ],

            'currency' => [
                'nullable',
                'string',
                'max:10',
            ],

            'logo' => [
                'nullable',
                'string',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}
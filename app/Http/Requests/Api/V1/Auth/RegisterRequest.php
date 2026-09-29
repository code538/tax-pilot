<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | User
            |--------------------------------------------------------------------------
            */

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            /*
            |--------------------------------------------------------------------------
            | Organisation
            |--------------------------------------------------------------------------
            */

            'organisation_name' => [
                'required',
                'string',
                'max:255',
            ],

            'organisation_slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                'unique:organisations,slug',
            ],

            'organisation_email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'organisation_phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'organisation_type' => [
                'nullable',
                'string',
                'max:50',
            ],
        ];
    }
}
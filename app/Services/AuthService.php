<?php

namespace App\Services;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuthService
{
    public function __construct(
        protected RoleService $roleService
    ) {
    }

    /**
     * Register user + organisation + owner role.
     */
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | Create User
            |--------------------------------------------------------------------------
            */

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            /*
            |--------------------------------------------------------------------------
            | Create Organisation
            |--------------------------------------------------------------------------
            */

            $organisation = Organisation::create([
                'name' => $data['organisation_name'],
                'slug' => $data['organisation_slug'],

                'email' => $data['organisation_email'] ?? null,
                'phone' => $data['organisation_phone'] ?? null,

                'type' => $data['organisation_type']
                    ?? 'tax_agent',

                'status' => 'active',
                'country' => 'United Kingdom',
                'timezone' => 'Europe/London',
                'currency' => 'GBP',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Create Owner Role
            |--------------------------------------------------------------------------
            */

            $ownerRole = $this->roleService
                ->createOwnerRole($organisation);

            /*
            |--------------------------------------------------------------------------
            | Create Organisation Membership
            |--------------------------------------------------------------------------
            */

            $organisation->users()->attach($user->id, [
                'status' => 'active',
                'joined_at' => now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Assign Owner Role
            |--------------------------------------------------------------------------
            */

            $ownerRole->users()->attach($user->id, [
                'organisation_id' => $organisation->id,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Create Sanctum Token
            |--------------------------------------------------------------------------
            */

            $token = $user
                ->createToken('tax-filing-api')
                ->plainTextToken;

            return [
                'user' => $user->fresh(),

                'organisation' => $organisation->fresh(),

                'role' => $ownerRole->load('permissions'),

                'token' => $token,

                'token_type' => 'Bearer',
            ];
        });
    }
}
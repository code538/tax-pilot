<?php

namespace App\Services;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OrganisationUserService
{
    public function all(
        Organisation $organisation
    ): Collection {
        return $organisation->users()
            ->with('roles')
            ->latest('organisation_users.id')
            ->get();
    }

    public function find(
        Organisation $organisation,
        int $userId
    ): User {
        return $organisation->users()
            ->with('roles')
            ->where('users.id', $userId)
            ->firstOrFail();
    }

    public function create(
        Organisation $organisation,
        array $data
    ): User {
        return DB::transaction(function () use (
            $organisation,
            $data
        ) {
            $user = User::where(
                'email',
                $data['email']
            )->first();

            if (!$user) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'is_super_admin' => false,
                ]);
            } else {
                $user->update([
                    'name' => $data['name'],
                ]);
            }

            $organisation->users()->syncWithoutDetaching([
                $user->id => [
                    'status' => 'active',
                    'joined_at' => now(),
                ],
            ]);

            if (!empty($data['role_id'])) {
                $this->assignRole(
                    $organisation,
                    $user,
                    (int) $data['role_id']
                );
            }

            return $user->fresh([
                'organisations',
                'roles',
            ]);
        });
    }

    public function update(
        Organisation $organisation,
        User $user,
        array $data
    ): User {
        return DB::transaction(function () use (
            $organisation,
            $user,
            $data
        ) {
            $userData = [];

            if (array_key_exists('name', $data)) {
                $userData['name'] = $data['name'];
            }

            if (array_key_exists('email', $data)) {
                $userData['email'] = $data['email'];
            }

            if (!empty($userData)) {
                $user->update($userData);
            }

            if (array_key_exists('status', $data)) {
                $organisation->users()->updateExistingPivot(
                    $user->id,
                    [
                        'status' => $data['status'],
                    ]
                );
            }

            if (array_key_exists('role_id', $data)) {
                $this->assignRole(
                    $organisation,
                    $user,
                    $data['role_id']
                );
            }

            return $user->fresh([
                'organisations',
                'roles',
            ]);
        });
    }

    public function delete(
        Organisation $organisation,
        User $user
    ): void {
        DB::transaction(function () use (
            $organisation,
            $user
        ) {
            /*
             * Remove only this organisation membership.
             */
            $organisation->users()->detach(
                $user->id
            );

            /*
             * Remove roles belonging to this organisation.
             */
            $user->roles()
                ->where(
                    'roles.organisation_id',
                    $organisation->id
                )
                ->get()
                ->each(function ($role) use ($user, $organisation) {
                    $user->roles()->detach(
                        $role->id,
                        [
                            'organisation_id' => $organisation->id,
                        ]
                    );
                });
        });
    }

    public function assignRole(
        Organisation $organisation,
        User $user,
        ?int $roleId
    ): void {
        if (!$roleId) {
            return;
        }

        $role = $organisation->roles()
            ->where('id', $roleId)
            ->where('is_active', true)
            ->firstOrFail();

        /*
         * Remove existing roles for this organisation.
         */
        $existingRoleIds = $user->roles()
            ->where(
                'roles.organisation_id',
                $organisation->id
            )
            ->pluck('roles.id');

        foreach ($existingRoleIds as $existingRoleId) {
            $user->roles()->detach(
                $existingRoleId
            );
        }

        /*
         * Assign the new organisation-specific role.
         */
        $role->users()->syncWithoutDetaching([
            $user->id => [
                'organisation_id' => $organisation->id,
            ],
        ]);
    }
}
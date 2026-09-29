<?php

namespace App\Services;

use App\Models\Organisation;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RoleService
{
    public function all(
        Organisation $organisation
    ): Collection {
        return $organisation->roles()
            ->with('permissions')
            ->latest()
            ->get();
    }

    public function find(
        Organisation $organisation,
        int $roleId
    ): Role {
        return $organisation->roles()
            ->with('permissions')
            ->where('id', $roleId)
            ->firstOrFail();
    }

    public function create(
        Organisation $organisation,
        array $data
    ): Role {
        return DB::transaction(function () use (
            $organisation,
            $data
        ) {
            $role = $organisation->roles()->create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'is_system' => false,
                'is_active' => true,
            ]);

            $permissionIds = $data['permission_ids'] ?? [];

            $validPermissionIds = Permission::query()
                ->where('is_active', true)
                ->whereIn('id', $permissionIds)
                ->pluck('id')
                ->toArray();

            $role->permissions()->sync(
                $validPermissionIds
            );

            return $role->fresh([
                'permissions',
            ]);
        });
    }

    public function update(
        Organisation $organisation,
        int $roleId,
        array $data
    ): Role {
        return DB::transaction(function () use (
            $organisation,
            $roleId,
            $data
        ) {
            $role = Role::query()
                ->where('id', $roleId)
                ->where(
                    'organisation_id',
                    $organisation->id
                )
                ->firstOrFail();

            // Never modify system roles such as Owner.
            if ($role->is_system) {
                abort(
                    422,
                    'System roles cannot be modified.'
                );
            }

            $roleData = [];

            if (array_key_exists('name', $data)) {
                $roleData['name'] = $data['name'];
            }

            if (array_key_exists('slug', $data)) {
                $roleData['slug'] = $data['slug'];
            }

            if (array_key_exists('description', $data)) {
                $roleData['description'] = $data['description'];
            }

            if (!empty($roleData)) {
                $role->update($roleData);
            }

            if (array_key_exists('permission_ids', $data)) {

                $validPermissionIds = Permission::query()
                    ->where('is_active', true)
                    ->whereIn(
                        'id',
                        $data['permission_ids'] ?? []
                    )
                    ->pluck('id')
                    ->toArray();

                $role->permissions()->sync(
                    $validPermissionIds
                );
            }

            return $role->fresh([
                'permissions',
            ]);
        });
    }

    public function delete(
        Organisation $organisation,
        Role $role
    ): void {
        DB::transaction(function () use (
            $organisation,
            $role
        ) {
            if ($role->organisation_id !== $organisation->id) {
                abort(404);
            }

            /*
             * System roles such as Owner cannot be deleted.
             */
            if ($role->is_system) {
                abort(
                    422,
                    'System roles cannot be deleted.'
                );
            }

            /*
             * Do not delete a role while users
             * are still assigned to it.
             */
            if ($role->users()->exists()) {
                abort(
                    422,
                    'This role is assigned to users and cannot be deleted.'
                );
            }

            $role->permissions()->detach();

            $role->delete();
        });
    }

    public function createOwnerRole(
        Organisation $organisation
    ): Role {
        $role = Role::updateOrCreate(
            [
                'organisation_id' => $organisation->id,
                'slug' => 'owner',
            ],
            [
                'name' => 'Owner',
                'description' => 'Full access to the organisation.',
                'is_system' => true,
                'is_active' => true,
            ]
        );

        $permissionIds = Permission::where(
            'is_active',
            true
        )->pluck('id');

        $role->permissions()->sync(
            $permissionIds
        );

        return $role->load('permissions');
    }
}
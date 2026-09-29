<?php

namespace App\Services\Admin;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Collection;

class SuperAdminService
{
    public function allOrganisations(): Collection
    {
        return Organisation::query()
            ->withCount('users')
            ->latest()
            ->get();
    }

    public function findOrganisation(
        int $organisationId
    ): Organisation {
        return Organisation::query()
            ->withCount('users')
            ->findOrFail($organisationId);
    }

    public function deleteOrganisation(
        Organisation $organisation
    ): bool {
        return (bool) $organisation->delete();
    }
}
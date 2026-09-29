<?php

namespace App\Services;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Collection;

class OrganisationService
{
    /**
     * Get all organisations.
     */
    public function all(): Collection
    {
        return Organisation::query()
            ->latest()
            ->get();
    }

    /**
     * Find organisation.
     */
    public function find(int $id): Organisation
    {
        return Organisation::findOrFail($id);
    }

    /**
     * Create organisation.
     */
    public function create(array $data): Organisation
    {
        return Organisation::create($data);
    }

    /**
     * Update organisation.
     */
    public function update(
        Organisation $organisation,
        array $data
    ): Organisation {
        $organisation->update($data);

        return $organisation->refresh();
    }

    /**
     * Delete organisation.
     */
    public function delete(Organisation $organisation): bool
    {
        return (bool) $organisation->delete();
    }
}
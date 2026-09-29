<?php

namespace App\Services;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class OrganisationContext
{
    protected ?Organisation $organisation = null;

    public function set(Organisation $organisation): void
    {
        $this->organisation = $organisation;
    }

    public function get(): ?Organisation
    {
        return $this->organisation;
    }

    public function id(): ?int
    {
        return $this->organisation?->id;
    }

    public function require(): Organisation
    {
        if (!$this->organisation) {
            throw new AuthorizationException(
                'Organisation context has not been set.'
            );
        }

        return $this->organisation;
    }

    public function userCanAccess(
        User $user,
        Organisation $organisation
    ): bool {
        if ($user->is_super_admin) {
            return true;
        }

        return $user->organisations()
            ->where('organisations.id', $organisation->id)
            ->wherePivot('status', 'active')
            ->exists();
    }
}
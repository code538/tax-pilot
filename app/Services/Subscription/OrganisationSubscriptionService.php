<?php

namespace App\Services\Subscription;

use App\Models\OrganisationSubscription;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class OrganisationSubscriptionService
{
    /**
     * Get the currently active subscription for an organisation.
     */
    public function getCurrentSubscription(
        int $organisationId
    ): ?OrganisationSubscription {
        return OrganisationSubscription::query()
            ->where('organisation_id', $organisationId)
            ->where('status', 'active')
            ->where(function (Builder $query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $query) {
                $query->whereNull('current_period_ends_at')
                    ->orWhere('current_period_ends_at', '>=', now());
            })
            ->with([
                'plan',
                'price',
            ])
            ->latest('id')
            ->first();
    }

    /**
     * Get subscription history for an organisation.
     */
    public function getSubscriptionHistory(
        int $organisationId,
        int $perPage = 15
    ): LengthAwarePaginator {
        return OrganisationSubscription::query()
            ->where('organisation_id', $organisationId)
            ->with([
                'plan',
                'price',
            ])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Find one subscription belonging to an organisation.
     */
    public function findForOrganisation(
        int $organisationId,
        int $subscriptionId
    ): OrganisationSubscription {
        return OrganisationSubscription::query()
            ->where('organisation_id', $organisationId)
            ->with([
                'plan',
                'price',
            ])
            ->whereKey($subscriptionId)
            ->firstOrFail();
    }
}
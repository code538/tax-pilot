<?php

namespace App\Services\Subscription;

use App\Models\OrganisationSubscription;
use App\Models\SubscriptionPlanEntitlement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class SubscriptionEntitlementService
{
    /**
     * Get the organisation's currently active subscription.
     */
    public function getActiveSubscription(
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
                $query->whereNull('current_period_end')
                    ->orWhere('current_period_end', '>=', now());
            })
            ->with('plan.entitlements')
            ->latest('id')
            ->first();
    }

    /**
     * Get an entitlement record for an organisation.
     */
    public function getEntitlement(
        int $organisationId,
        string $key
    ): ?SubscriptionPlanEntitlement {
        $subscription = $this->getActiveSubscription($organisationId);

        if (! $subscription || ! $subscription->plan) {
            return null;
        }

        return $subscription->plan->entitlements
            ->firstWhere('entitlement_key', $key);
    }

    /**
     * Get the value of an entitlement.
     *
     * Returns null when the subscription or entitlement is missing.
     */
    public function getValue(
        int $organisationId,
        string $key
    ): mixed {
        $entitlement = $this->getEntitlement(
            $organisationId,
            $key
        );

        return $entitlement?->value;
    }

    /**
     * Check whether a boolean feature is enabled.
     */
    public function hasFeature(
        int $organisationId,
        string $key
    ): bool {
        $entitlement = $this->getEntitlement(
            $organisationId,
            $key
        );

        if (! $entitlement || $entitlement->value_type !== 'boolean') {
            return false;
        }

        return $entitlement->value === true;
    }

    /**
     * Get an integer usage limit.
     *
     * Returns null if the subscription or entitlement is missing.
     * Do not interpret null as unlimited automatically.
     */
    public function getLimit(
        int $organisationId,
        string $key
    ): ?int {
        $entitlement = $this->getEntitlement(
            $organisationId,
            $key
        );

        if (! $entitlement || $entitlement->value_type !== 'integer') {
            return null;
        }

        $value = $entitlement->value;

        if (! is_int($value)) {
            return null;
        }

        return $value;
    }

    /**
     * Check whether the organisation may add another resource.
     *
     * Example:
     * canAddResource($organisationId, 'companies.limit', $companyCount)
     */
    public function canAddResource(
        int $organisationId,
        string $limitKey,
        int $currentCount
    ): bool {
        $limit = $this->getLimit(
            $organisationId,
            $limitKey
        );

        // Fail closed when no valid subscription limit exists.
        if ($limit === null || $limit < 0) {
            return false;
        }

        return $currentCount < $limit;
    }
}
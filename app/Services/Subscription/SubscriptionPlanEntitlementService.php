<?php

namespace App\Services\Subscription;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanEntitlement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SubscriptionPlanEntitlementService
{
    public function getAllForPlan(
        int $planId,
        int $perPage = 15
    ): LengthAwarePaginator {
        SubscriptionPlan::query()->findOrFail($planId);

        return SubscriptionPlanEntitlement::query()
            ->where('subscription_plan_id', $planId)
            ->orderBy('entitlement_key')
            ->paginate($perPage);
    }

    public function createForPlan(
        SubscriptionPlan $plan,
        array $data
    ): SubscriptionPlanEntitlement {
        return SubscriptionPlanEntitlement::create([
            'subscription_plan_id' => $plan->id,
            'entitlement_key' => $data['entitlement_key'],
            'value_type' => $data['value_type'],
            'value' => $data['value'],
        ]);
    }

    public function findForPlan(
        int $planId,
        int $entitlementId
    ): SubscriptionPlanEntitlement {
        return SubscriptionPlanEntitlement::query()
            ->where('subscription_plan_id', $planId)
            ->whereKey($entitlementId)
            ->firstOrFail();
    }

    public function update(
        SubscriptionPlanEntitlement $entitlement,
        array $data
    ): SubscriptionPlanEntitlement {
        $entitlement->update($data);

        return $entitlement->refresh();
    }

    public function delete(
        SubscriptionPlanEntitlement $entitlement
    ): void {
        $entitlement->delete();
    }
}
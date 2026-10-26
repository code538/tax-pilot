<?php

namespace App\Services\Subscription;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanPrice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SubscriptionPlanPriceAdminService
{
    public function getAllForPlan(
        int $planId,
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {
        SubscriptionPlan::query()->findOrFail($planId);

        return SubscriptionPlanPrice::query()
            ->where('subscription_plan_id', $planId)
            ->when(
                array_key_exists('is_active', $filters),
                fn ($query) => $query->where(
                    'is_active',
                    $filters['is_active']
                )
            )
            ->when(
                isset($filters['billing_interval']),
                fn ($query) => $query->where(
                    'billing_interval',
                    $filters['billing_interval']
                )
            )
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function createForPlan(
        SubscriptionPlan $plan,
        array $data
    ): SubscriptionPlanPrice {
        $data['subscription_plan_id'] = $plan->id;

        return SubscriptionPlanPrice::create($data);
    }

    public function findForPlan(
        int $planId,
        int $priceId
    ): SubscriptionPlanPrice {
        return SubscriptionPlanPrice::query()
            ->where('subscription_plan_id', $planId)
            ->whereKey($priceId)
            ->firstOrFail();
    }

    public function update(
        SubscriptionPlanPrice $price,
        array $data
    ): SubscriptionPlanPrice {
        $price->update($data);

        return $price->refresh();
    }

    public function delete(SubscriptionPlanPrice $price): void
    {
        $price->delete();
    }
}
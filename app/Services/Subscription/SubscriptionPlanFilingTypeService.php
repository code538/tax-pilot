<?php

namespace App\Services\Subscription;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanFilingType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionPlanFilingTypeService
{
    public function getAllForPlan(
        int $planId,
        int $perPage = 15
    ): LengthAwarePaginator {
        SubscriptionPlan::query()->findOrFail($planId);

        return SubscriptionPlanFilingType::query()
            ->with('filingType')
            ->where('subscription_plan_id', $planId)
            ->orderBy('id')
            ->paginate($perPage);
    }

    public function createForPlan(
        SubscriptionPlan $plan,
        array $data
    ): SubscriptionPlanFilingType {
        return SubscriptionPlanFilingType::create([
            'subscription_plan_id' => $plan->id,
            'filing_type_id' => $data['filing_type_id'],
        ])->load('filingType');
    }

    public function findForPlan(
        int $planId,
        int $mappingId
    ): SubscriptionPlanFilingType {
        return SubscriptionPlanFilingType::query()
            ->with('filingType')
            ->where('subscription_plan_id', $planId)
            ->whereKey($mappingId)
            ->firstOrFail();
    }

    public function update(
        SubscriptionPlanFilingType $mapping,
        array $data
    ): SubscriptionPlanFilingType {
        $mapping->update($data);

        return $mapping->refresh()->load('filingType');
    }

    public function delete(
        SubscriptionPlanFilingType $mapping
    ): void {
        $mapping->delete();
    }
}
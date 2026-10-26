<?php

namespace App\Services\Subscription;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanFeature;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SubscriptionPlanFeatureService
{
    public function getAll(
        int $planId,
        int $perPage = 15
    ): LengthAwarePaginator {
        $this->findPlan($planId);

        return SubscriptionPlanFeature::query()
            ->where('subscription_plan_id', $planId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($perPage);
    }

    public function create(
        int $planId,
        array $data
    ): SubscriptionPlanFeature {
        $this->findPlan($planId);

        $data['subscription_plan_id'] = $planId;

        return SubscriptionPlanFeature::create($data);
    }

    public function find(
        int $planId,
        int $featureId
    ): SubscriptionPlanFeature {
        return SubscriptionPlanFeature::query()
            ->where('subscription_plan_id', $planId)
            ->whereKey($featureId)
            ->firstOrFail();
    }

    public function update(
        int $planId,
        int $featureId,
        array $data
    ): SubscriptionPlanFeature {
        $feature = $this->find($planId, $featureId);

        $feature->update($data);

        return $feature->refresh();
    }

    public function delete(
        int $planId,
        int $featureId
    ): void {
        $feature = $this->find($planId, $featureId);

        $feature->delete();
    }

    protected function findPlan(int $planId): SubscriptionPlan
    {
        return SubscriptionPlan::query()->findOrFail($planId);
    }
}

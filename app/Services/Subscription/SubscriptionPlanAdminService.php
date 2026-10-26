<?php

namespace App\Services\Subscription;

use App\Models\SubscriptionPlan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class SubscriptionPlanAdminService
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return SubscriptionPlan::query()
            ->with(['product', 'activePrices', 'filingTypes', 'entitlements'])
            ->when(
                $filters['subscription_product_id'] ?? null,
                fn ($query, $productId) => $query->where(
                    'subscription_product_id',
                    $productId
                )
            )
            ->when(
                isset($filters['is_active']),
                fn ($query) => $query->where(
                    'is_active',
                    $filters['is_active']
                )
            )
            ->when(
                $filters['search'] ?? null,
                fn ($query, $search) => $query->where(
                    fn ($q) => $q
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                )
            )
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function find(int $id): SubscriptionPlan
    {
        return SubscriptionPlan::with([
            'product',
            'prices',
            'filingTypes',
            'entitlements',
        ])->findOrFail($id);
    }

    public function create(array $data): SubscriptionPlan
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        return SubscriptionPlan::create($data);
    }

    public function update(
        SubscriptionPlan $plan,
        array $data
    ): SubscriptionPlan {
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $plan->update($data);

        return $plan->refresh();
    }

    public function delete(SubscriptionPlan $plan): void
    {
        // Keep plan records that are referenced by subscriptions.
        if ($plan->organisationSubscriptions()->exists()) {
            abort(
                422,
                'This plan has subscription history. Deactivate it instead of deleting it.'
            );
        }

        $plan->delete();
    }
}
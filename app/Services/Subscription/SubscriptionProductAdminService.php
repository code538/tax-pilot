<?php

namespace App\Services\Subscription;

use App\Models\SubscriptionProduct;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class SubscriptionProductAdminService
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return SubscriptionProduct::query()
            ->when(
                $filters['search'] ?? null,
                fn ($query, $search) => $query->where(
                    fn ($q) => $q
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                )
            )
            ->when(
                isset($filters['is_active']),
                fn ($query) => $query->where(
                    'is_active',
                    $filters['is_active']
                )
            )
            ->withCount('plans')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function find(int $id): SubscriptionProduct
    {
        return SubscriptionProduct::with([
            'plans.prices',
            'plans.filingTypes',
            'plans.entitlements',
        ])->findOrFail($id);
    }

    public function create(array $data): SubscriptionProduct
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        return SubscriptionProduct::create($data);
    }

    public function update(
        SubscriptionProduct $product,
        array $data
    ): SubscriptionProduct {
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $product->update($data);

        return $product->refresh();
    }

    public function delete(SubscriptionProduct $product): void
    {
        // Do not delete a product that has plans.
        if ($product->plans()->exists()) {
            abort(
                422,
                'This product has plans. Deactivate it instead of deleting it.'
            );
        }

        $product->delete();
    }
}
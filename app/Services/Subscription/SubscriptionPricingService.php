<?php

namespace App\Services\Subscription;

use App\Models\SubscriptionProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionPricingService
{
    /**
     * Get all active products with active plans and prices.
     */
    public function getActiveProducts(): Collection
    {
        return SubscriptionProduct::query()
            ->where('is_active', true)
            ->with([
                'activePlans' => function (Builder $query) {
                    $query->orderBy('sort_order')
                        ->orderBy('id');
                },
                'activePlans.activePrices' => function (Builder $query) {
                    $query->where(function (Builder $q) {
                        $q->whereNull('effective_from')
                            ->orWhere('effective_from', '<=', now());
                    })->where(function (Builder $q) {
                        $q->whereNull('effective_until')
                            ->orWhere('effective_until', '>', now());
                    });
                },
                'activePlans.filingTypes',
                'activePlans.entitlements',
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * Get one active product by slug.
     */
    public function getActiveProductBySlug(
        string $slug
    ): ?SubscriptionProduct {
        return SubscriptionProduct::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'activePlans' => function (Builder $query) {
                    $query->orderBy('sort_order')
                        ->orderBy('id');
                },
                'activePlans.activePrices' => function (Builder $query) {
                    $query->where(function (Builder $q) {
                        $q->whereNull('effective_from')
                            ->orWhere('effective_from', '<=', now());
                    })->where(function (Builder $q) {
                        $q->whereNull('effective_until')
                            ->orWhere('effective_until', '>', now());
                    });
                },
                'activePlans.filingTypes',
                'activePlans.entitlements',
            ])
            ->first();
    }
}
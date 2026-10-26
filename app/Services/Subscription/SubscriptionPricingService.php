<?php

namespace App\Services\Subscription;

use App\Models\SubscriptionProduct;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionPricingService
{
    /**
     * Get all active subscription products with their active plans.
     */
    public function getActiveProducts(): Collection
    {
        return SubscriptionProduct::query()
            ->where('is_active', true)
            ->with([
                'activePlans' => function ($query) {
                    $query->orderBy('sort_order')
                        ->orderBy('name')
                        ->with([
                            'activePrices' => function ($priceQuery) {
                                $priceQuery
                                    ->where(function ($query) {
                                        $query->whereNull('effective_from')
                                            ->orWhere('effective_from', '<=', now());
                                    })
                                    ->where(function ($query) {
                                        $query->whereNull('effective_until')
                                            ->orWhere('effective_until', '>', now());
                                    })
                                    ->orderBy('amount_minor');
                            },
                            'filingTypes',
                            'entitlements',
                            'features',
                        ]);
                },
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get one active product by slug.
     */
    public function getActiveProductBySlug(string $slug): ?SubscriptionProduct
    {
        return SubscriptionProduct::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'activePlans' => function ($query) {
                    $query->orderBy('sort_order')
                        ->orderBy('name')
                        ->with([
                            'activePrices' => function ($priceQuery) {
                                $priceQuery
                                    ->where(function ($query) {
                                        $query->whereNull('effective_from')
                                            ->orWhere('effective_from', '<=', now());
                                    })
                                    ->where(function ($query) {
                                        $query->whereNull('effective_until')
                                            ->orWhere('effective_until', '>', now());
                                    })
                                    ->orderBy('amount_minor');
                            },
                            'filingTypes',
                            'entitlements',
                            'features',
                        ]);
                },
            ])
            ->first();
    }
}

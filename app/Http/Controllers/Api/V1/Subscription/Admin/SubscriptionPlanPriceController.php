<?php

namespace App\Http\Controllers\Api\V1\Subscription\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Subscription\SubscriptionPlanPriceAdminResource;
use App\Models\SubscriptionPlan;
use App\Services\Subscription\SubscriptionPlanPriceAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SubscriptionPlanPriceController extends Controller
{
    public function __construct(
        protected SubscriptionPlanPriceAdminService $service
    ) {
    }

    /**
     * List prices for a plan.
     */
    public function index(Request $request, int $planId)
    {
        $filters = $request->validate([
            'is_active' => ['sometimes', 'boolean'],
            'billing_interval' => [
                'sometimes',
                Rule::in(['daily', 'weekly', 'monthly', 'quarterly', 'yearly']),
            ],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $prices = $this->service->getAllForPlan(
            $planId,
            $filters,
            $filters['per_page'] ?? 15
        );

        return SubscriptionPlanPriceAdminResource::collection($prices);
    }

    /**
     * Create a price for a plan.
     */
    public function store(Request $request, int $planId): JsonResponse
    {
        $plan = SubscriptionPlan::query()->findOrFail($planId);

        $data = $request->validate([
            'billing_interval' => [
                'required',
                Rule::in(['daily', 'weekly', 'monthly', 'quarterly', 'yearly']),
            ],
            'interval_count' => ['sometimes', 'integer', 'min:1'],
            'amount_minor' => ['required', 'integer', 'min:0'],
            'currency' => ['sometimes', 'string', 'size:3', 'alpha'],
            'stripe_product_id' => ['sometimes', 'nullable', 'string', 'max:255'],
            'stripe_price_id' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                'unique:subscription_plan_prices,stripe_price_id',
            ],
            'is_active' => ['sometimes', 'boolean'],
            'effective_from' => ['sometimes', 'nullable', 'date'],
            'effective_until' => ['sometimes', 'nullable', 'date'],
        ]);

        $effectiveFrom = $data['effective_from'] ?? null;
        $effectiveUntil = $data['effective_until'] ?? null;

        if (
            $effectiveFrom !== null &&
            $effectiveUntil !== null &&
            strtotime($effectiveUntil) <= strtotime($effectiveFrom)
        ) {
            throw ValidationException::withMessages([
                'effective_until' => [
                    'The effective_until date must be later than effective_from.',
                ],
            ]);
        }

        $data['interval_count'] = $data['interval_count'] ?? 1;
        $data['currency'] = strtoupper($data['currency'] ?? 'GBP');
        $data['is_active'] = $data['is_active'] ?? true;

        $price = $this->service->createForPlan($plan, $data);

        return (new SubscriptionPlanPriceAdminResource($price))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Show one price belonging to a plan.
     */
    public function show(
        int $planId,
        int $priceId
    ): SubscriptionPlanPriceAdminResource {
        return new SubscriptionPlanPriceAdminResource(
            $this->service->findForPlan($planId, $priceId)
        );
    }

    /**
     * Update a price.
     */
    public function update(
        Request $request,
        int $planId,
        int $priceId
    ): SubscriptionPlanPriceAdminResource {
        $price = $this->service->findForPlan($planId, $priceId);

        $data = $request->validate([
            'billing_interval' => [
                'sometimes',
                Rule::in(['daily', 'weekly', 'monthly', 'quarterly', 'yearly']),
            ],
            'interval_count' => ['sometimes', 'integer', 'min:1'],
            'amount_minor' => ['sometimes', 'integer', 'min:0'],
            'currency' => ['sometimes', 'string', 'size:3', 'alpha'],
            'stripe_product_id' => ['sometimes', 'nullable', 'string', 'max:255'],
            'stripe_price_id' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                Rule::unique('subscription_plan_prices', 'stripe_price_id')
                    ->ignore($price->id),
            ],
            'is_active' => ['sometimes', 'boolean'],
            'effective_from' => ['sometimes', 'nullable', 'date'],
            'effective_until' => ['sometimes', 'nullable', 'date'],
        ]);

        $effectiveFrom = array_key_exists('effective_from', $data)
            ? $data['effective_from']
            : $price->effective_from;

        $effectiveUntil = array_key_exists('effective_until', $data)
            ? $data['effective_until']
            : $price->effective_until;

        if (
            $effectiveFrom !== null &&
            $effectiveUntil !== null &&
            strtotime((string) $effectiveUntil) <= strtotime((string) $effectiveFrom)
        ) {
            throw ValidationException::withMessages([
                'effective_until' => [
                    'The effective_until date must be later than effective_from.',
                ],
            ]);
        }

        if (isset($data['currency'])) {
            $data['currency'] = strtoupper($data['currency']);
        }

        $updatedPrice = $this->service->update($price, $data);

        return new SubscriptionPlanPriceAdminResource($updatedPrice);
    }

    /**
     * Delete a price only if no organisation subscription uses it.
     */
    public function destroy(
        int $planId,
        int $priceId
    ): JsonResponse {
        $price = $this->service->findForPlan($planId, $priceId);

        if ($price->organisationSubscriptions()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This price is linked to an organisation subscription and cannot be deleted.',
            ], 422);
        }

        $this->service->delete($price);

        return response()->json([
            'success' => true,
            'message' => 'Subscription plan price deleted successfully.',
        ]);
    }
}
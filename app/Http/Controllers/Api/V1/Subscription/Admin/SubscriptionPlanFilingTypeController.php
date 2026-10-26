<?php

namespace App\Http\Controllers\Api\V1\Subscription\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Subscription\SubscriptionPlanFilingTypeResource;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanFilingType;
use App\Services\Subscription\SubscriptionPlanFilingTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionPlanFilingTypeController extends Controller
{
    public function __construct(
        protected SubscriptionPlanFilingTypeService $service
    ) {
    }

    /**
     * List filing types assigned to a subscription plan.
     */
    public function index(
        Request $request,
        int $planId
    ) {
        $validated = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $mappings = $this->service->getAllForPlan(
            $planId,
            (int) ($validated['per_page'] ?? 15)
        );

        return SubscriptionPlanFilingTypeResource::collection($mappings);
    }

    /**
     * Assign a filing type to a subscription plan.
     */
    public function store(
        Request $request,
        int $planId
    ): JsonResponse {
        $plan = SubscriptionPlan::query()->findOrFail($planId);

        $validated = $request->validate([
            'filing_type_id' => [
                'required',
                'integer',
                'exists:filing_types,id',
                Rule::unique('subscription_plan_filing_types', 'filing_type_id')
                    ->where('subscription_plan_id', $plan->id),
            ],
        ]);

        $mapping = $this->service->createForPlan($plan, $validated);

        return (new SubscriptionPlanFilingTypeResource($mapping))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Show one filing type mapping.
     */
    public function show(
        int $planId,
        int $mappingId
    ): SubscriptionPlanFilingTypeResource {
        $mapping = $this->service->findForPlan($planId, $mappingId);

        return new SubscriptionPlanFilingTypeResource($mapping);
    }

    /**
     * Update a filing type mapping.
     */
    public function update(
        Request $request,
        int $planId,
        int $mappingId
    ): SubscriptionPlanFilingTypeResource {
        $mapping = $this->service->findForPlan($planId, $mappingId);

        $validated = $request->validate([
            'filing_type_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:filing_types,id',
                Rule::unique('subscription_plan_filing_types', 'filing_type_id')
                    ->where('subscription_plan_id', $planId)
                    ->ignore($mapping->id),
            ],
        ]);

        $mapping = $this->service->update($mapping, $validated);

        return new SubscriptionPlanFilingTypeResource($mapping);
    }

    /**
     * Remove a filing type mapping.
     */
    public function destroy(
        int $planId,
        int $mappingId
    ): JsonResponse {
        $mapping = $this->service->findForPlan($planId, $mappingId);

        $this->service->delete($mapping);

        return response()->json([
            'success' => true,
            'message' => 'Filing type removed from subscription plan successfully.',
        ]);
    }
}
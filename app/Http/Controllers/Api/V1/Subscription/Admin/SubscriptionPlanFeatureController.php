<?php

namespace App\Http\Controllers\Api\V1\Subscription\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Subscription\SubscriptionPlanFeatureResource;
use App\Services\Subscription\SubscriptionPlanFeatureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionPlanFeatureController extends Controller
{
    public function __construct(
        protected SubscriptionPlanFeatureService $service
    ) {
    }

    /**
     * List features for a subscription plan.
     */
    public function index(Request $request, int $planId)
    {
        $data = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $features = $this->service->getAll(
            $planId,
            $data['per_page'] ?? 15
        );

        return SubscriptionPlanFeatureResource::collection($features);
    }

    /**
     * Create a feature for a subscription plan.
     */
    public function store(Request $request, int $planId): JsonResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'is_included' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $feature = $this->service->create($planId, $data);

        return (new SubscriptionPlanFeatureResource($feature))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Show one feature belonging to the specified plan.
     */
    public function show(
        int $planId,
        int $featureId
    ): SubscriptionPlanFeatureResource {
        return new SubscriptionPlanFeatureResource(
            $this->service->find($planId, $featureId)
        );
    }

    /**
     * Update a feature.
     */
    public function update(
        Request $request,
        int $planId,
        int $featureId
    ): SubscriptionPlanFeatureResource {
        $data = $request->validate([
            'label' => ['sometimes', 'required', 'string', 'max:255'],
            'is_included' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $feature = $this->service->update(
            $planId,
            $featureId,
            $data
        );

        return new SubscriptionPlanFeatureResource($feature);
    }

    /**
     * Delete a feature.
     */
    public function destroy(
        int $planId,
        int $featureId
    ): JsonResponse {
        $this->service->delete($planId, $featureId);

        return response()->json([
            'success' => true,
            'message' => 'Subscription plan feature deleted successfully.',
        ]);
    }
}

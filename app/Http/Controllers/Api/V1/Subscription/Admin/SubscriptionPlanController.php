<?php

namespace App\Http\Controllers\Api\V1\Subscription\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Subscription\SubscriptionPlanResource;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionProduct;
use App\Services\Subscription\SubscriptionPlanAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionPlanController extends Controller
{
    public function __construct(
        protected SubscriptionPlanAdminService $service
    ) {
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'subscription_product_id' => [
                'nullable',
                'integer',
                'exists:subscription_products,id',
            ],
            'search' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        return SubscriptionPlanResource::collection(
            $this->service->getAll($filters)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subscription_product_id' => [
                'required',
                'integer',
                'exists:subscription_products,id',
            ],

            'name' => ['required', 'string', 'max:255'],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                'alpha_dash',
                'unique:subscription_plans,slug',
            ],

            'description' => ['nullable', 'string'],

            // Pricing-card display fields
            'tagline' => ['nullable', 'string', 'max:255'],
            'badge_text' => ['nullable', 'string', 'max:100'],
            'button_text' => ['sometimes', 'required', 'string', 'max:100'],
            'display_group' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::in(['main', 'larger_practices']),
            ],

            'is_active' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $product = SubscriptionProduct::findOrFail(
            $data['subscription_product_id']
        );

        if (!$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot add a plan to an inactive product.',
            ], 422);
        }

        $plan = $this->service->create($data);

        return (new SubscriptionPlanResource(
            $this->service->find($plan->id)
        ))->response()->setStatusCode(201);
    }

    public function show(int $id): SubscriptionPlanResource
    {
        return new SubscriptionPlanResource(
            $this->service->find($id)
        );
    }

    public function update(
        Request $request,
        int $id
    ): JsonResponse|SubscriptionPlanResource {
        $plan = SubscriptionPlan::findOrFail($id);

        $data = $request->validate([
            'subscription_product_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:subscription_products,id',
            ],

            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('subscription_plans', 'slug')
                    ->ignore($plan->id),
            ],

            'description' => ['sometimes', 'nullable', 'string'],

            // Pricing-card display fields
            'tagline' => ['sometimes', 'nullable', 'string', 'max:255'],
            'badge_text' => ['sometimes', 'nullable', 'string', 'max:100'],
            'button_text' => [
                'sometimes',
                'required',
                'string',
                'max:100',
            ],
            'display_group' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::in(['main', 'larger_practices']),
            ],

            'is_active' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $productId = $data['subscription_product_id']
            ?? $plan->subscription_product_id;

        $product = SubscriptionProduct::findOrFail($productId);

        $willBeActive = $data['is_active'] ?? $plan->is_active;

        if (!$product->is_active && $willBeActive) {
            return response()->json([
                'success' => false,
                'message' => 'Activate the product before assigning an active plan to it.',
            ], 422);
        }

        $updatedPlan = $this->service->update($plan, $data);

        return new SubscriptionPlanResource($updatedPlan);
    }

    public function destroy(int $id): JsonResponse
    {
        $plan = SubscriptionPlan::findOrFail($id);

        $this->service->delete($plan);

        return response()->json([
            'success' => true,
            'message' => 'Subscription plan deleted successfully.',
        ]);
    }
}

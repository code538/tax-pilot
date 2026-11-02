<?php

namespace App\Http\Controllers\Api\V1\Subscription\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Subscription\SubscriptionPlanEntitlementAdminResource;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanEntitlement;
use App\Services\Subscription\SubscriptionPlanEntitlementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SubscriptionPlanEntitlementController extends Controller
{
    public function __construct(
        protected SubscriptionPlanEntitlementService $service
    ) {
    }

    /**
     * List entitlements for a subscription plan.
     */
    public function index(Request $request, int $planId)
    {
        $validated = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $entitlements = $this->service->getAllForPlan(
            $planId,
            (int) ($validated['per_page'] ?? 15)
        );

        return SubscriptionPlanEntitlementAdminResource::collection(
            $entitlements
        );
    }

    /**
     * Create an entitlement for a subscription plan.
     */
    public function store(
        Request $request,
        int $planId
    ): JsonResponse {
        $plan = SubscriptionPlan::query()->findOrFail($planId);

        $validated = $request->validate(
            [
                'entitlement_key' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('subscription_plan_entitlements', 'entitlement_key')
                        ->where('subscription_plan_id', $plan->id),
                ],
                'value_type' => [
                    'required',
                    Rule::in(['boolean', 'integer', 'string', 'json']),
                ],
                'value' => ['present', 'required'],
            ],
            [
                'entitlement_key.required' => 'Entitlement key is required.',
                'entitlement_key.unique' => 'This entitlement already exists for this plan.',
                'value_type.in' => 'Value type must be boolean, integer, string, or json.',
                'value.required' => 'Entitlement value is required.',
            ]
        );

        $this->validateValueType(
            $validated['value_type'],
            $validated['value']
        );

        $entitlement = $this->service->createForPlan(
            $plan,
            $validated
        );

        return (new SubscriptionPlanEntitlementAdminResource($entitlement))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Show one entitlement.
     */
    public function show(
        int $planId,
        int $entitlementId
    ): SubscriptionPlanEntitlementAdminResource {
        $entitlement = $this->service->findForPlan(
            $planId,
            $entitlementId
        );

        return new SubscriptionPlanEntitlementAdminResource($entitlement);
    }

    /**
     * Update an entitlement.
     */
    public function update(
        Request $request,
        int $planId,
        int $entitlementId
    ): SubscriptionPlanEntitlementAdminResource {
        $entitlement = $this->service->findForPlan(
            $planId,
            $entitlementId
        );

        $validated = $request->validate(
            [
                'entitlement_key' => [
                    'sometimes',
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('subscription_plan_entitlements', 'entitlement_key')
                        ->where('subscription_plan_id', $planId)
                        ->ignore($entitlement->id),
                ],
                'value_type' => [
                    'sometimes',
                    'required',
                    Rule::in(['boolean', 'integer', 'string', 'json']),
                ],
                'value' => ['sometimes', 'present', 'required'],
            ],
            [
                'entitlement_key.unique' => 'This entitlement already exists for this plan.',
                'value_type.in' => 'Value type must be boolean, integer, string, or json.',
                'value.required' => 'Entitlement value is required.',
            ]
        );

        $finalType = $validated['value_type']
            ?? $entitlement->value_type;

        $finalValue = array_key_exists('value', $validated)
            ? $validated['value']
            : $entitlement->value;

        $this->validateValueType($finalType, $finalValue);

        $entitlement = $this->service->update(
            $entitlement,
            $validated
        );

        return new SubscriptionPlanEntitlementAdminResource($entitlement);
    }

    /**
     * Delete an entitlement.
     */
    public function destroy(
        int $planId,
        int $entitlementId
    ): JsonResponse {
        $entitlement = $this->service->findForPlan(
            $planId,
            $entitlementId
        );

        $this->service->delete($entitlement);

        return response()->json([
            'success' => true,
            'message' => 'Subscription plan entitlement deleted successfully.',
        ]);
    }

    /**
     * Ensure value matches its declared type.
     */
    private function validateValueType(
        string $type,
        mixed $value
    ): void {
        $valid = match ($type) {
            'boolean' => is_bool($value),
            'integer' => is_int($value),
            'string' => is_string($value),
            'json' => is_array($value) || is_object($value),
            default => false,
        };

        if (! $valid) {
            throw ValidationException::withMessages([
                'value' => [
                    "The value must match the declared {$type} type.",
                ],
            ]);
        }
    }
}
<?php

namespace App\Http\Controllers\Api\V1\Subscription;

use App\Http\Controllers\Controller;
use App\Http\Resources\Subscription\OrganisationSubscriptionResource;
use App\Services\Subscription\OrganisationSubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\OrganisationSubscription;
use App\Services\Payment\PayPalService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;


class OrganisationSubscriptionController extends Controller
{
    public function __construct(
        protected OrganisationSubscriptionService $service,
        protected PayPalService $paypalService
    ) {
    }

    /**
     * Get the current subscription of an organisation.
     */
    public function current(
        int $organisationId
    ): JsonResponse|OrganisationSubscriptionResource {
        //dd(now());
        $subscription = $this->service->getCurrentSubscription(
            $organisationId
        );
       
        if (! $subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found for this organisation.',
                'data' => null,
            ], 404);
        }

        return new OrganisationSubscriptionResource($subscription);
    }

    
    // public function current(
    //     int $organisationId
    // ): JsonResponse|OrganisationSubscriptionResource {

    //     // Step 1: Check route parameter
    //     // dd($organisationId);

    //     // Step 2: Run query directly without service
    //     $subscription = OrganisationSubscription::query()
    //         ->where('organisation_id', $organisationId)
    //         ->where('status', 'active')
    //         ->where(function (Builder $query) {
    //             $query->whereNull('starts_at')
    //                 ->orWhere('starts_at', '<=', now());
    //         })
    //         ->where(function (Builder $query) {
    //             $query->whereNull('current_period_ends_at')
    //                 ->orWhere('current_period_ends_at', '>=', now());
    //         })
    //         ->with([
    //             'plan',
    //             'price',
    //         ])
    //         ->latest('id')
    //         ->first();

    //     // Step 3: Check query result
    //     // dd($subscription);

    //     if (! $subscription) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'No active subscription found for this organisation.',
    //             'data' => null,
    //         ], 404);
    //     }

    //     // Step 4: Return subscription resource
    //     return new OrganisationSubscriptionResource($subscription);
    // }


    /**
     * Get subscription history for an organisation.
     */
    public function history(
        Request $request,
        int $organisationId
    ) {
        $validated = $request->validate([
            'per_page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],
        ]);

        $subscriptions = $this->service->getSubscriptionHistory(
            $organisationId,
            (int) ($validated['per_page'] ?? 15)
        );

        return OrganisationSubscriptionResource::collection(
            $subscriptions
        );
    }

    /**
     * Get one subscription belonging to an organisation.
     */
    public function show(
        int $organisationId,
        int $subscriptionId
    ): OrganisationSubscriptionResource {
        $subscription = $this->service->findForOrganisation(
            $organisationId,
            $subscriptionId
        );

        return new OrganisationSubscriptionResource($subscription);
    }


   

    public function success(
        Request $request,
        PayPalService $paypalService
    ): JsonResponse {
        $paypalSubscriptionId = $request->query('subscription_id');

        if (empty($paypalSubscriptionId)) {
            return response()->json([
                'success' => false,
                'message' => 'PayPal subscription ID is missing.',
            ], 422);
        }

        try {
            // Find the subscription created by your checkout controller.
            $localSubscription = OrganisationSubscription::query()
                ->where('provider', 'paypal')
                ->where(
                    'provider_subscription_id',
                    $paypalSubscriptionId
                )
                ->firstOrFail();

            // Verify the actual subscription status with PayPal.
            $paypalSubscription = $paypalService->getSubscription(
                $paypalSubscriptionId
            );

            $paypalStatus = $paypalSubscription['status'] ?? null;

            // Map PayPal status to your local subscription status.
            $status = match ($paypalStatus) {
                'ACTIVE' => 'active',
                'APPROVAL_PENDING' => 'pending',
                'SUSPENDED' => 'suspended',
                'CANCELLED', 'EXPIRED' => 'cancelled',
                default => 'pending',
            };

            DB::transaction(function () use (
                $localSubscription,
                $paypalSubscription,
                $status
            ) {
                $localSubscription->update([
                    'status' => $status,
                    'starts_at' => $paypalSubscription['start_time']
                        ?? now(),
                    'current_period_starts_at' => now(),
                    'current_period_ends_at' =>
                        $paypalSubscription['billing_info']['next_billing_time']
                        ?? null,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => $status === 'active'
                    ? 'Subscription activated successfully.'
                    : 'PayPal subscription status updated.',
                'data' => [
                    'subscription_id' => $localSubscription->id,
                    'status' => $status,
                    'paypal_status' => $paypalStatus,
                ],
            ]);

        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Unable to verify PayPal subscription.',
            ], 500);
        }
    }
}
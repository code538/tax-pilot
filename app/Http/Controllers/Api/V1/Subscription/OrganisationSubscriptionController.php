<?php

namespace App\Http\Controllers\Api\V1\Subscription;

use App\Http\Controllers\Controller;
use App\Http\Resources\Subscription\OrganisationSubscriptionResource;
use App\Services\Subscription\OrganisationSubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class OrganisationSubscriptionController extends Controller
{
    public function __construct(
        protected OrganisationSubscriptionService $service
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
}
<?php

namespace App\Http\Controllers\Api\V1\Organisation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Organisation\StoreInvitationRequest;
use App\Http\Resources\OrganisationInvitationResource;
use App\Services\ApiResponseService;
use App\Services\OrganisationContext;
use App\Services\OrganisationInvitationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganisationInvitationController
    extends Controller
{
    public function __construct(
        protected OrganisationContext $context,
        protected OrganisationInvitationService $invitationService,
        protected ApiResponseService $response
    ) {
    }

    public function index(): JsonResponse
    {
        $organisation = $this->context->require();

        $invitations = $this->invitationService->all(
            $organisation
        );

        return $this->response->success(
            'Organisation invitations retrieved successfully.',
            OrganisationInvitationResource::collection(
                $invitations
            )
        );
    }

    public function store(
        StoreInvitationRequest $request
    ): JsonResponse {
        $organisation = $this->context->require();

        $invitation = $this->invitationService->create(
            $organisation,
            $request->user()->id,
            $request->validated()
        );

        return $this->response->success(
            'Invitation created successfully.',
            new OrganisationInvitationResource(
                $invitation
            ),
            201
        );
    }

    public function cancel(
        int $invitationId
    ): JsonResponse {
        $organisation = $this->context->require();

        $invitation = $this->invitationService->cancel(
            $organisation,
            $invitationId
        );

        return $this->response->success(
            'Invitation cancelled successfully.',
            new OrganisationInvitationResource(
                $invitation
            )
        );
    }
}
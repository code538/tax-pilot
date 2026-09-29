<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\AcceptInvitationRequest;
use App\Http\Resources\OrganisationInvitationResource;
use App\Services\ApiResponseService;
use App\Services\OrganisationInvitationService;
use Illuminate\Http\JsonResponse;

class InvitationController extends Controller
{
    public function __construct(
        protected OrganisationInvitationService $invitationService,
        protected ApiResponseService $response
    ) {
    }

    public function accept(
        AcceptInvitationRequest $request,
        string $token
    ): JsonResponse {

        $result = $this->invitationService->accept(
            $token,
            $request->validated()
        );

        return $this->response->success(
            'Invitation accepted successfully.',
            $result
        );
    }
}
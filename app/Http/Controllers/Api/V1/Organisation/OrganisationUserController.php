<?php

namespace App\Http\Controllers\Api\V1\Organisation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Organisation\StoreOrganisationUserRequest;
use App\Http\Requests\Api\V1\Organisation\UpdateOrganisationUserRequest;
use App\Services\ApiResponseService;
use App\Services\OrganisationContext;
use App\Services\OrganisationUserService;
use Illuminate\Http\JsonResponse;

class OrganisationUserController extends Controller
{
    public function __construct(
        protected OrganisationContext $context,
        protected OrganisationUserService $userService,
        protected ApiResponseService $response
    ) {
    }

    public function index(): JsonResponse
    {   
        $organisation = $this->context->require();
        //dd($organisation);
        $users = $this->userService->all(
            $organisation
        );

        return $this->response->success(
            'Organisation users retrieved successfully.',
            $users
        );
    }

    public function store(
        StoreOrganisationUserRequest $request
    ): JsonResponse {
        $organisation = $this->context->require();

        $user = $this->userService->create(
            $organisation,
            $request->validated()
        );

        return $this->response->success(
            'Organisation user created successfully.',
            $user,
            201
        );
    }

    public function show(
        int $userId
    ): JsonResponse {
        $organisation = $this->context->require();

        $user = $this->userService->find(
            $organisation,
            $userId
        );

        return $this->response->success(
            'Organisation user retrieved successfully.',
            $user
        );
    }

    public function update(
        UpdateOrganisationUserRequest $request,
        int $userId
    ): JsonResponse {
        $organisation = $this->context->require();

        $user = $this->userService->find(
            $organisation,
            $userId
        );

        $user = $this->userService->update(
            $organisation,
            $user,
            $request->validated()
        );

        return $this->response->success(
            'Organisation user updated successfully.',
            $user
        );
    }

    public function destroy(
        int $userId
    ): JsonResponse {
        $organisation = $this->context->require();

        $user = $this->userService->find(
            $organisation,
            $userId
        );

        $this->userService->delete(
            $organisation,
            $user
        );

        return $this->response->success(
            'Organisation user removed successfully.'
        );
    }
}
<?php

namespace App\Http\Controllers\Api\V1\Organisation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Organisation\StoreRoleRequest;
use App\Http\Requests\Api\V1\Organisation\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Services\ApiResponseService;
use App\Services\OrganisationContext;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function __construct(
        protected OrganisationContext $context,
        protected RoleService $roleService,
        protected ApiResponseService $response
    ) {
    }

    public function index(): JsonResponse
    {
        $organisation = $this->context->require();

        $roles = $this->roleService->all(
            $organisation
        );

        return $this->response->success(
            'Organisation roles retrieved successfully.',
            RoleResource::collection($roles)
        );
    }

    public function store(
        StoreRoleRequest $request
    ): JsonResponse {
        $organisation = $this->context->require();

        $role = $this->roleService->create(
            $organisation,
            $request->validated()
        );

        return $this->response->success(
            'Role created successfully.',
            new RoleResource($role),
            201
        );
    }

    public function show(
        int $roleId
    ): JsonResponse {
        $organisation = $this->context->require();

        $role = $this->roleService->find(
            $organisation,
            $roleId
        );

        return $this->response->success(
            'Role retrieved successfully.',
            new RoleResource($role)
        );
    }

    public function update(
        UpdateRoleRequest $request,
        int $roleId
    ): JsonResponse {
        $organisation = $this->context->require();

        $role = $this->roleService->update(
            $organisation,
            $roleId,
            $request->validated()
        );

        return $this->response->success(
            'Role updated successfully.',
            new RoleResource($role)
        );
    }

    public function destroy(
        int $roleId
    ): JsonResponse {
        $organisation = $this->context->require();

        $role = $this->roleService->find(
            $organisation,
            $roleId
        );

        $this->roleService->delete(
            $organisation,
            $role
        );

        return $this->response->success(
            'Role deleted successfully.'
        );
    }
}
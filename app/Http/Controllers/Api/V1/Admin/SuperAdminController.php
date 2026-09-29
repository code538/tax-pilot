<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\SuperAdminService;
use App\Services\ApiResponseService;
use Illuminate\Http\JsonResponse;

class SuperAdminController extends Controller
{
    public function __construct(
        protected SuperAdminService $adminService,
        protected ApiResponseService $response
    ) {
    }

    public function organisations(): JsonResponse
    {
        $organisations = $this->adminService
            ->allOrganisations();

        return $this->response->success(
            'Organisations retrieved successfully.',
            $organisations
        );
    }

    public function organisation(
        int $organisationId
    ): JsonResponse {
        $organisation = $this->adminService
            ->findOrganisation($organisationId);

        return $this->response->success(
            'Organisation retrieved successfully.',
            $organisation
        );
    }

    public function deleteOrganisation(
        int $organisationId
    ): JsonResponse {
        $organisation = $this->adminService
            ->findOrganisation($organisationId);

        $this->adminService
            ->deleteOrganisation($organisation);

        return $this->response->success(
            'Organisation deleted successfully.'
        );
    }
}
<?php

namespace App\Http\Controllers\Api\V1\Organisation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Organisation\UpdateOrganisationRequest;
use App\Models\Organisation;
use App\Services\ApiResponseService;
use App\Services\OrganisationContext;
use App\Services\OrganisationService;
use Illuminate\Http\JsonResponse;

class OrganisationController extends Controller
{
    public function __construct(
        protected OrganisationService $organisationService,
        protected OrganisationContext $context,
        protected ApiResponseService $response
    ) {
    }

    public function show(): JsonResponse
    {
        $organisation = $this->context->require();

        return $this->response->success(
            'Organisation retrieved successfully.',
            $organisation
        );
    }

    public function update(
        UpdateOrganisationRequest $request
    ): JsonResponse {
        $organisation = $this->context->require();

        $organisation = $this->organisationService->update(
            $organisation,
            $request->validated()
        );

        return $this->response->success(
            'Organisation updated successfully.',
            $organisation
        );
    }

    public function destroy(): JsonResponse
    {
        $organisation = $this->context->require();

        $this->organisationService->delete(
            $organisation
        );

        return $this->response->success(
            'Organisation deleted successfully.'
        );
    }

    public function myOrganisations(): JsonResponse
    {
        //dd('okk');
        $organisations = auth()->user()
            ->organisations()
            ->wherePivot('status', 'active')
            ->latest('organisations.id')
            ->get();
        //dd($organisations);
        return $this->response->success(
            'Organisations retrieved successfully.',
            $organisations
        );
    }
}
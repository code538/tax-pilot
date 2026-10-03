<?php

namespace App\Http\Controllers\Api\V1\Filing;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFilingVersionRequest;
use App\Http\Resources\FilingVersionResource;
use App\Models\Filing;
use App\Services\FilingVersionService;
use App\Services\OrganisationContext;
use App\Services\ApiResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FilingVersionController extends Controller
{
    public function __construct(
        private FilingVersionService $filingVersionService,
        private OrganisationContext $context,
        private ApiResponseService $response
    ) {
    }

    public function index(
        int $organisationId,
        int $filingId
    ): JsonResponse {
        $organisation = $this->context->require();

        $filing = Filing::query()
            ->where('id', $filingId)
            ->where('organisation_id', $organisation->id)
            ->firstOrFail();

        $versions = $this->filingVersionService->all(
            $organisation,
            $filing
        );

        return $this->response->success(
            'Filing versions retrieved successfully.',
            FilingVersionResource::collection($versions)
        );
    }

    public function store(
        StoreFilingVersionRequest $request,
        int $organisationId,
        int $filingId
    ): JsonResponse {
        $organisation = $this->context->require();

        $filing = Filing::query()
            ->where('id', $filingId)
            ->where('organisation_id', $organisation->id)
            ->firstOrFail();

        $version = $this->filingVersionService->create(
            $organisation,
            $filing,
            $request->validated(),
            $request->user()->id
        );

        return $this->response->success(
            'Filing version created successfully.',
            new FilingVersionResource($version),
            201
        );
    }

    public function show(
        int $organisationId,
        int $filingId,
        int $versionId
    ): JsonResponse {
        $organisation = $this->context->require();

        $filing = Filing::query()
            ->where('id', $filingId)
            ->where('organisation_id', $organisation->id)
            ->firstOrFail();

        $version = $this->filingVersionService->find(
            $organisation,
            $filing,
            $versionId
        );

        return $this->response->success(
            'Filing version retrieved successfully.',
            new FilingVersionResource($version)
        );
    }
}

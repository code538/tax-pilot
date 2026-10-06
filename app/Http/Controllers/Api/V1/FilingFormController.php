<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FilingFormResource;
use App\Services\ApiResponseService;
use App\Services\FilingFormService;
use Illuminate\Http\JsonResponse;

class FilingFormController extends Controller
{
    public function __construct(
        private FilingFormService $filingFormService,
        private ApiResponseService $response
    ) {}

    public function show(
        int $organisationId,
        int $filingId
    ): JsonResponse {
        $filingVersion = $this->filingFormService->getForm(
            $organisationId,
            $filingId
        );

        return $this->response->success(
            'Filing form retrieved successfully.',
            new FilingFormResource($filingVersion)
        );
    }
}
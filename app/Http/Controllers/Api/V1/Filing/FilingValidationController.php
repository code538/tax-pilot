<?php

namespace App\Http\Controllers\Api\V1\Filing;

use App\Http\Controllers\Controller;
use App\Services\ApiResponseService;
use App\Services\FilingValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FilingValidationController extends Controller
{
    public function __construct(
        protected FilingValidationService $filingValidationService,
        protected ApiResponseService $apiResponse
    ) {}

    /**
     * Validate the latest version of a filing.
     */
    public function validate(
        Request $request,
        $organisationId,
        $filingId
    ): JsonResponse {
        $result = $this->filingValidationService->validateFiling(
            (int) $organisationId,
            (int) $filingId,
            (int) $request->user()->id
        );

        return $this->apiResponse->success(
            $result->status === 'passed'
                ? 'Filing validation passed.'
                : 'Filing validation failed. Please correct the errors.',
            $result,
            200
        );
    }
}
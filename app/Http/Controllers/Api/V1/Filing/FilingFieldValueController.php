<?php

namespace App\Http\Controllers\Api\V1\Filing;

use App\Http\Controllers\Controller;
use App\Services\ApiResponseService;
use App\Services\FilingFieldValueService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FilingFieldValueController extends Controller
{
    public function __construct(
        protected FilingFieldValueService $filingFieldValueService,
        protected ApiResponseService $apiResponse
    ) {}

    /**
     * Get saved values for a filing version.
     */
    public function index(
        $organisationId,
        $filingId,
        $filingVersionId
    ) {
        $values = $this->filingFieldValueService->all(
            $organisationId,
            $filingId,
            $filingVersionId
        );

        return $this->apiResponse->success(
            'Filing field values retrieved successfully.',
            $values
        );
    }

    /**
     * Save/update filing field values.
     */
    public function store(
        Request $request,
        $organisationId,
        $filingId,
        $filingVersionId
    ) {
        $validated = $request->validate([
            'values' => [
                'required',
                'array',
                'min:1',
            ],

            'values.*.form_field_id' => [
                'required',
                'integer',
            ],

            'values.*.value' => [
                'nullable',
            ],
        ]);

        $values = $this->filingFieldValueService->save(
            $organisationId,
            $filingId,
            $filingVersionId,
            $validated['values']
        );

        return $this->apiResponse->success(
            'Filing field values saved successfully.',
            $values
        );
    }
}
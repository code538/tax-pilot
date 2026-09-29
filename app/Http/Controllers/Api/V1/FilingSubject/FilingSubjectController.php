<?php

namespace App\Http\Controllers\Api\V1\FilingSubject;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\FilingSubject\StoreFilingSubjectRequest;
use App\Http\Requests\Api\V1\FilingSubject\UpdateFilingSubjectRequest;
use App\Http\Resources\FilingSubjectResource;
use App\Services\ApiResponseService;
use App\Services\FilingSubject\FilingSubjectService;
use App\Services\OrganisationContext;
use Illuminate\Http\JsonResponse;

class FilingSubjectController extends Controller
{
    public function __construct(
        protected FilingSubjectService $filingSubjectService,
        protected OrganisationContext $context,
        protected ApiResponseService $response
    ) {}

    public function index(): JsonResponse
    {
        $organisation = $this->context->require();

        $subjects = $this->filingSubjectService
            ->all($organisation);

        return $this->response->success(
            'Filing subjects retrieved successfully.',
            FilingSubjectResource::collection($subjects)
        );
    }

    public function store(
        StoreFilingSubjectRequest $request
    ): JsonResponse {
        $organisation = $this->context->require();

        $subject = $this->filingSubjectService->create(
            $organisation,
            $request->validated()
        );

        return $this->response->success(
            'Filing subject created successfully.',
            new FilingSubjectResource($subject),
            201
        );
    }

    public function show(int $subjectId): JsonResponse
    {
        $organisation = $this->context->require();

        $subject = $this->filingSubjectService->find(
            $organisation,
            $subjectId
        );

        return $this->response->success(
            'Filing subject retrieved successfully.',
            new FilingSubjectResource($subject)
        );
    }

    public function update(
        UpdateFilingSubjectRequest $request,
        int $subjectId
    ): JsonResponse {
        $organisation = $this->context->require();

        $subject = $this->filingSubjectService->update(
            $organisation,
            $subjectId,
            $request->validated()
        );

        return $this->response->success(
            'Filing subject updated successfully.',
            new FilingSubjectResource($subject)
        );
    }

    public function destroy(int $subjectId): JsonResponse
    {
        $organisation = $this->context->require();

        $this->filingSubjectService->delete(
            $organisation,
            $subjectId
        );

        return $this->response->success(
            'Filing subject deleted successfully.'
        );
    }
}
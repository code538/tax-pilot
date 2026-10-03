<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormSectionRequest;
use App\Http\Requests\UpdateFormSectionRequest;
use App\Http\Resources\FormSectionResource;
use App\Services\ApiResponseService;
use App\Services\FormSectionService;
use Illuminate\Http\JsonResponse;

class FormSectionController extends Controller
{
    public function __construct(
        private FormSectionService $formSectionService,
        private ApiResponseService $response
    ) {}

    public function index(int $formVersionId): JsonResponse
    {
        $sections = $this->formSectionService->all($formVersionId);

        return $this->response->success(
            'Form sections retrieved successfully.',
            FormSectionResource::collection($sections)
        );
    }

    public function store(
        StoreFormSectionRequest $request,
        int $formVersionId
    ): JsonResponse {
        $section = $this->formSectionService->create(
            $formVersionId,
            $request->validated()
        );

        return $this->response->success(
            'Form section created successfully.',
            new FormSectionResource($section),
            201
        );
    }

    public function show(
        int $formVersionId,
        int $formSectionId
    ): JsonResponse {
        $section = $this->formSectionService->find(
            $formVersionId,
            $formSectionId
        );

        return $this->response->success(
            'Form section retrieved successfully.',
            new FormSectionResource($section)
        );
    }

    public function update(
        UpdateFormSectionRequest $request,
        int $formVersionId,
        int $formSectionId
    ): JsonResponse {
        $section = $this->formSectionService->update(
            $formVersionId,
            $formSectionId,
            $request->validated()
        );

        return $this->response->success(
            'Form section updated successfully.',
            new FormSectionResource($section)
        );
    }

    public function destroy(
        int $formVersionId,
        int $formSectionId
    ): JsonResponse {
        $this->formSectionService->delete(
            $formVersionId,
            $formSectionId
        );

        return $this->response->success(
            'Form section deleted successfully.'
        );
    }
}
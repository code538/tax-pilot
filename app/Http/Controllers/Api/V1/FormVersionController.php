<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormVersionRequest;
use App\Http\Resources\FormVersionResource;
use App\Models\FormVersion;
use App\Services\ApiResponseService;
use App\Services\FormVersionService;
use Illuminate\Http\JsonResponse;

class FormVersionController extends Controller
{
    public function __construct(
        private FormVersionService $formVersionService,
        private ApiResponseService $response
    ) {
    }

    public function index(): JsonResponse
    {
        $versions = $this->formVersionService->all();

        return $this->response->success(
            'Form versions retrieved successfully.',
            FormVersionResource::collection($versions)
        );
    }

    public function store(
        StoreFormVersionRequest $request
    ): JsonResponse {
        $version = $this->formVersionService->create(
            (int) $request->validated('form_definition_id'),
            (int) $request->user()->id
        );

        return $this->response->success(
            'Draft form version created successfully.',
            new FormVersionResource($version),
            201
        );
    }

    public function show(int $formVersionId): JsonResponse
    {
        $version = $this->formVersionService->find($formVersionId);

        return $this->response->success(
            'Form version retrieved successfully.',
            new FormVersionResource($version)
        );
    }

    public function publish(int $formVersionId): JsonResponse
    {
        $version = $this->formVersionService->publish($formVersionId);

        return $this->response->success(
            'Form version published successfully.',
            new FormVersionResource($version)
        );
    }

    public function destroy(int $formVersionId): JsonResponse
    {
        $this->formVersionService->deleteDraft($formVersionId);

        return $this->response->success(
            'Draft form version deleted successfully.'
        );
    }
}
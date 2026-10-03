<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormDefinitionRequest;
use App\Http\Requests\UpdateFormDefinitionRequest;
use App\Http\Resources\FormDefinitionResource;
use App\Services\ApiResponseService;
use App\Services\FormDefinitionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FormDefinitionController extends Controller
{
      public function __construct(
        private FormDefinitionService $formDefinitionService,
        private ApiResponseService $response
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'filing_type_id' => ['sometimes', 'integer', 'min:1'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $forms = $this->formDefinitionService->all($filters);

        return $this->response->success(
            'Form definitions retrieved successfully.',
            FormDefinitionResource::collection($forms)
        );
    }

    public function store(
        StoreFormDefinitionRequest $request
    ): JsonResponse {
        $form = $this->formDefinitionService->create(
            $request->validated()
        );

        return $this->response->success(
            'Form definition created successfully.',
            new FormDefinitionResource($form),
            201
        );
    }

    public function show(int $formDefinitionId): JsonResponse
    {
        $form = $this->formDefinitionService->find(
            $formDefinitionId
        );

        return $this->response->success(
            'Form definition retrieved successfully.',
            new FormDefinitionResource($form)
        );
    }

    public function update(
        UpdateFormDefinitionRequest $request,
        int $formDefinitionId
    ): JsonResponse {
        $form = $this->formDefinitionService->update(
            $formDefinitionId,
            $request->validated()
        );

        return $this->response->success(
            'Form definition updated successfully.',
            new FormDefinitionResource($form)
        );
    }

    public function destroy(int $formDefinitionId): JsonResponse
    {
        $form = $this->formDefinitionService->deactivate(
            $formDefinitionId
        );

        return $this->response->success(
            'Form definition deactivated successfully.',
            new FormDefinitionResource($form)
        );
    }
}

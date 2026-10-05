<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormFieldRequest;
use App\Http\Requests\UpdateFormFieldRequest;
use App\Http\Resources\FormFieldResource;
use App\Services\ApiResponseService;
use App\Services\FormFieldService;
use Illuminate\Http\JsonResponse;

class FormFieldController extends Controller
{
    public function __construct(
        private FormFieldService $formFieldService,
        private ApiResponseService $response
    ) {}

    public function index(
        int $formVersionId,
        int $formSectionId
    ): JsonResponse {
        $fields = $this->formFieldService->all(
            $formVersionId,
            $formSectionId
        );

        return $this->response->success(
            'Form fields retrieved successfully.',
            FormFieldResource::collection($fields)
        );
    }

    public function store(
        StoreFormFieldRequest $request,
        int $formVersionId,
        int $formSectionId
    ): JsonResponse {
        $field = $this->formFieldService->create(
            $formVersionId,
            $formSectionId,
            $request->validated()
        );

        return $this->response->success(
            'Form field created successfully.',
            new FormFieldResource($field),
            201
        );
    }

    public function show(
        int $formVersionId,
        int $formSectionId,
        int $formFieldId
    ): JsonResponse {
        $field = $this->formFieldService->find(
            $formVersionId,
            $formSectionId,
            $formFieldId
        );

        return $this->response->success(
            'Form field retrieved successfully.',
            new FormFieldResource($field)
        );
    }

    public function update(
        UpdateFormFieldRequest $request,
        int $formVersionId,
        int $formSectionId,
        int $formFieldId
    ): JsonResponse {
        $field = $this->formFieldService->update(
            $formVersionId,
            $formSectionId,
            $formFieldId,
            $request->validated()
        );

        return $this->response->success(
            'Form field updated successfully.',
            new FormFieldResource($field)
        );
    }

    public function destroy(
        int $formVersionId,
        int $formSectionId,
        int $formFieldId
    ): JsonResponse {
        $this->formFieldService->delete(
            $formVersionId,
            $formSectionId,
            $formFieldId
        );

        return $this->response->success(
            'Form field deleted successfully.'
        );
    }
}
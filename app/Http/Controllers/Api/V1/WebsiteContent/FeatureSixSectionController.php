<?php

namespace App\Http\Controllers\Api\V1\WebsiteContent;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebsiteContent\FeatureSixSectionResource;
use App\Services\WebsiteContent\FeatureSixSectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FeatureSixSectionController extends Controller
{
    public function __construct(
        protected FeatureSixSectionService $service
    ) {}

    /**
     * Public: Get active steps by slug.
     */
    public function publicShow(
        string $slug
    ): AnonymousResourceCollection {
        return FeatureSixSectionResource::collection(
            $this->service->getActiveBySlug($slug)
        );
    }

    /**
     * Admin: List steps with optional filters.
     */
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        $filters = $request->validate([
            'page_title' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        return FeatureSixSectionResource::collection(
            $this->service->getAll($filters)
        );
    }

    /**
     * Admin: Get one step.
     */
    public function show(int $id): FeatureSixSectionResource
    {
        return new FeatureSixSectionResource(
            $this->service->find($id)
        );
    }

    /**
     * Admin: Create a step.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'page_title' => ['required', 'string', 'max:255'],
            'section_title' => ['nullable', 'string', 'max:255'],
            'section_description' => ['nullable', 'string'],
            'step_count' => ['required', 'integer', 'min:1'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $section = $this->service->create($data);

        return response()->json([
            'message' => 'Feature six section created successfully.',
            'data' => new FeatureSixSectionResource($section),
        ], 201);
    }

    /**
     * Admin: Update a step.
     */
    public function update(
        Request $request,
        int $id
    ): JsonResponse {
        $section = $this->service->find($id);

        $data = $request->validate([
            'page_title' => ['sometimes', 'required', 'string', 'max:255'],
            'section_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'section_description' => ['sometimes', 'nullable', 'string'],
            'step_count' => ['sometimes', 'required', 'integer', 'min:1'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $section = $this->service->update($section, $data);

        return response()->json([
            'message' => 'Feature six section updated successfully.',
            'data' => new FeatureSixSectionResource($section),
        ]);
    }

    /**
     * Admin: Delete a step.
     */
    public function destroy(int $id): JsonResponse
    {
        $section = $this->service->find($id);

        $this->service->delete($section);

        return response()->json([
            'message' => 'Feature six section deleted successfully.',
        ]);
    }
}
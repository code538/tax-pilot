<?php

namespace App\Http\Controllers\Api\V1\WebsiteContent;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebsiteContent\FeatureForthSectionResource;
use App\Services\WebsiteContent\FeatureForthSectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FeatureForthSectionController extends Controller
{
    public function __construct(
        protected FeatureForthSectionService $service
    ) {}

    /**
     * Public: Get active content by slug.
     */
    public function publicShow(
        string $slug
    ): AnonymousResourceCollection {
        return FeatureForthSectionResource::collection(
            $this->service->getActiveBySlug($slug)
        );
    }

    /**
     * Admin: List content with filters.
     */
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        $filters = $request->validate([
            'page_name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        return FeatureForthSectionResource::collection(
            $this->service->getAll($filters)
        );
    }

    /**
     * Admin: Get one record.
     */
    public function show(int $id): FeatureForthSectionResource
    {
        return new FeatureForthSectionResource(
            $this->service->find($id)
        );
    }

    /**
     * Admin: Create content.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'page_name' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'feature' => ['nullable', 'array'],
            'feature.*' => ['required', 'string', 'max:255'],

            'mobile_image' => ['nullable', 'string', 'max:2048'],
            'web_image' => ['nullable', 'string', 'max:2048'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $section = $this->service->create($data);

        return response()->json([
            'message' => 'Feature fourth section created successfully.',
            'data' => new FeatureForthSectionResource($section),
        ], 201);
    }

    /**
     * Admin: Update content.
     */
    public function update(
        Request $request,
        int $id
    ): JsonResponse {
        $section = $this->service->find($id);

        $data = $request->validate([
            'page_name' => ['sometimes', 'required', 'string', 'max:255'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],

            'feature' => ['sometimes', 'nullable', 'array'],
            'feature.*' => ['required', 'string', 'max:255'],

            'mobile_image' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'web_image' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'image_alt' => ['sometimes', 'nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $section = $this->service->update($section, $data);

        return response()->json([
            'message' => 'Feature fourth section updated successfully.',
            'data' => new FeatureForthSectionResource($section),
        ]);
    }

    /**
     * Admin: Delete content.
     */
    public function destroy(int $id): JsonResponse
    {
        $section = $this->service->find($id);

        $this->service->delete($section);

        return response()->json([
            'message' => 'Feature fourth section deleted successfully.',
        ]);
    }
}
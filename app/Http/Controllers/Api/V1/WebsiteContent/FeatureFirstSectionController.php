<?php

namespace App\Http\Controllers\Api\V1\WebsiteContent;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebsiteContent\FeatureFirstSectionResource;
use App\Models\FeatureFirstSection;
use App\Services\WebsiteContent\FeatureFirstSectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FeatureFirstSectionController extends Controller
{
    public function __construct(
        protected FeatureFirstSectionService $sectionService
    ) {}

    // Public: Get active sections matching a slug
    public function publicShow(string $slug): AnonymousResourceCollection
    {
        return FeatureFirstSectionResource::collection(
            $this->sectionService->getActiveBySlug($slug)
        );
    }

    // Admin: List sections with filters and pagination
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->validate([
            'page_title' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        return FeatureFirstSectionResource::collection(
            $this->sectionService->getAll($filters)
        );
    }

    // Admin: Get a section by ID
    public function show(int $id): FeatureFirstSectionResource
    {
        return new FeatureFirstSectionResource(
            $this->sectionService->find($id)
        );
    }

    // Admin: Create a section
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'page_title' => ['required', 'string', 'max:255'],
            'batch' => ['nullable', 'string', 'max:255'],
            'heading' => ['required', 'string', 'max:255'],
            'heading_meta' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'desc_meta' => ['nullable', 'string', 'max:255'],
            'primary_button' => ['nullable', 'string', 'max:255'],
            'primary_button_url' => ['nullable', 'string', 'max:2048'],
            'secondary_button' => ['nullable', 'string', 'max:255'],
            'secondary_button_url' => ['nullable', 'string', 'max:2048'],
            'trust_text' => ['nullable', 'string', 'max:255'],
            'web_image' => ['nullable', 'string', 'max:2048'],
            'mobile_image' => ['nullable', 'string', 'max:2048'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $section = $this->sectionService->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Website section created successfully.',
            'data' => new FeatureFirstSectionResource($section),
        ], 201);
    }

    // Admin: Update a section
    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'page_title' => ['sometimes', 'required', 'string', 'max:255'],
            'batch' => ['sometimes', 'nullable', 'string', 'max:255'],
            'heading' => ['sometimes', 'required', 'string', 'max:255'],
            'heading_meta' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'desc_meta' => ['sometimes', 'nullable', 'string', 'max:255'],
            'primary_button' => ['sometimes', 'nullable', 'string', 'max:255'],
            'primary_button_url' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'secondary_button' => ['sometimes', 'nullable', 'string', 'max:255'],
            'secondary_button_url' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'trust_text' => ['sometimes', 'nullable', 'string', 'max:255'],
            'web_image' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'mobile_image' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'image_alt' => ['sometimes', 'nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $section = $this->sectionService->update(
            $this->sectionService->find($id),
            $data
        );

        return response()->json([
            'success' => true,
            'message' => 'Website section updated successfully.',
            'data' => new FeatureFirstSectionResource($section),
        ]);
    }

    // Admin: Delete a section
    public function destroy(int $id): JsonResponse
    {
        $this->sectionService->delete(
            $this->sectionService->find($id)
        );

        return response()->json([
            'success' => true,
            'message' => 'Website section deleted successfully.',
        ]);
    }
}

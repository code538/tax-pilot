<?php

namespace App\Http\Controllers\Api\V1\WebsiteContent;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebsiteContent\FeatureSecondSectionResource;
use App\Services\WebsiteContent\FeatureSecondSectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FeatureSecondSectionController extends Controller
{
    public function __construct(
        protected FeatureSecondSectionService $sectionService
    ) {}

    // Public: Get active sections by page slug
    public function publicShow(string $slug): AnonymousResourceCollection
    {
        return FeatureSecondSectionResource::collection(
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

        return FeatureSecondSectionResource::collection(
            $this->sectionService->getAll($filters)
        );
    }

    // Admin: Get one section by ID
    public function show(int $id): FeatureSecondSectionResource
    {
        return new FeatureSecondSectionResource(
            $this->sectionService->find($id)
        );
    }

    // Admin: Create a section
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'page_title' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'heading' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $section = $this->sectionService->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Second section created successfully.',
            'data' => new FeatureSecondSectionResource($section),
        ], 201);
    }

    // Admin: Update a section
    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'page_title' => ['sometimes', 'required', 'string', 'max:255'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'heading' => ['sometimes', 'required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $section = $this->sectionService->update(
            $this->sectionService->find($id),
            $data
        );

        return response()->json([
            'success' => true,
            'message' => 'Second section updated successfully.',
            'data' => new FeatureSecondSectionResource($section),
        ]);
    }

    // Admin: Delete a section
    public function destroy(int $id): JsonResponse
    {
        $section = $this->sectionService->find($id);

        $this->sectionService->delete($section);

        return response()->json([
            'success' => true,
            'message' => 'Second section deleted successfully.',
        ]);
    }
}

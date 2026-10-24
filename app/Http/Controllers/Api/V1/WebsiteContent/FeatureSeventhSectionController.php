<?php

namespace App\Http\Controllers\Api\V1\WebsiteContent;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebsiteContent\FeatureSeventhSectionResource;
use App\Models\FeatureSeventhSection;
use App\Services\WebsiteContent\FeatureSeventhSectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FeatureSeventhSectionController extends Controller
{
    public function __construct(
        protected FeatureSeventhSectionService $service
    ) {}

    /**
     * Public: Get active section records by slug.
     */
    public function publicShow(
        string $slug
    ): AnonymousResourceCollection {
        $sections = $this->service->getActiveBySlug($slug);

        return FeatureSeventhSectionResource::collection($sections);
    }

    /**
     * Admin: List all records with pagination and filters.
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

        return FeatureSeventhSectionResource::collection(
            $this->service->getAll($filters)
        );
    }

    /**
     * Admin: Get one record.
     */
    public function show(
        int $id
    ): FeatureSeventhSectionResource {
        return new FeatureSeventhSectionResource(
            $this->service->find($id)
        );
    }

    /**
     * Admin: Create a record.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'page_title' => ['required', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255'],
            'section_title' => ['nullable', 'string', 'max:255'],
            'section_desc' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $section = $this->service->create($data);

        return response()->json([
            'message' => 'Feature seventh section created successfully.',
            'data' => new FeatureSeventhSectionResource($section),
        ], 201);
    }

    /**
     * Admin: Update a record.
     */
    public function update(
        Request $request,
        int $id
    ): JsonResponse {
        $section = $this->service->find($id);

        $data = $request->validate([
            'page_title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255'],
            'section_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'section_desc' => ['sometimes', 'nullable', 'string'],
            'icon' => ['sometimes', 'nullable', 'string', 'max:255'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $section = $this->service->update($section, $data);

        return response()->json([
            'message' => 'Feature seventh section updated successfully.',
            'data' => new FeatureSeventhSectionResource($section),
        ]);
    }

    /**
     * Admin: Delete a record.
     */
    public function destroy(int $id): JsonResponse
    {
        $section = $this->service->find($id);

        $this->service->delete($section);

        return response()->json([
            'message' => 'Feature seventh section deleted successfully.',
        ]);
    }
}

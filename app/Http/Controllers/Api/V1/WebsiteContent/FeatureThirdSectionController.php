<?php

namespace App\Http\Controllers\Api\V1\WebsiteContent;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebsiteContent\FeatureThirdSectionResource;
use App\Models\FeatureThirdSection;
use App\Services\WebsiteContent\FeatureThirdSectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FeatureThirdSectionController extends Controller
{
    public function __construct(
        protected FeatureThirdSectionService $service
    ) {}

    /**
     * Public: Get active feature cards by page slug.
     */
    public function publicShow(
        string $slug
    ): AnonymousResourceCollection {
        return FeatureThirdSectionResource::collection(
            $this->service->getActiveBySlug($slug)
        );
    }

    /**
     * Admin: List all feature cards with optional filters.
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

        return FeatureThirdSectionResource::collection(
            $this->service->getAll($filters)
        );
    }

    /**
     * Admin: Get one feature card.
     */
    public function show(
        int $id
    ): FeatureThirdSectionResource {
        return new FeatureThirdSectionResource(
            $this->service->find($id)
        );
    }

    /**
     * Admin: Create a feature card.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'page_title' => ['required', 'string', 'max:255'],
            'section_heading' => ['nullable', 'string', 'max:255'],
            'section_desc' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $section = $this->service->create($data);

        return response()->json([
            'message' => 'Feature third section created successfully.',
            'data' => new FeatureThirdSectionResource($section),
        ], 201);
    }

    /**
     * Admin: Update a feature card.
     */
    public function update(
        Request $request,
        int $id
    ): JsonResponse {
        $section = $this->service->find($id);

        $data = $request->validate([
            'page_title' => ['sometimes', 'required', 'string', 'max:255'],
            'section_heading' => ['sometimes', 'nullable', 'string', 'max:255'],
            'section_desc' => ['sometimes', 'nullable', 'string'],
            'icon' => ['sometimes', 'nullable', 'string', 'max:255'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $section = $this->service->update($section, $data);

        return response()->json([
            'message' => 'Feature third section updated successfully.',
            'data' => new FeatureThirdSectionResource($section),
        ]);
    }

    /**
     * Admin: Delete a feature card.
     */
    public function destroy(int $id): JsonResponse
    {
        $section = $this->service->find($id);

        $this->service->delete($section);

        return response()->json([
            'message' => 'Feature third section deleted successfully.',
        ]);
    }
}
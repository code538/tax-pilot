<?php

namespace App\Http\Controllers\Api\V1\WebsiteContent;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebsiteContent\FeatureFifthSectionResource;
use App\Services\WebsiteContent\FeatureFifthSectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FeatureFifthSectionController extends Controller
{
    public function __construct(
        protected FeatureFifthSectionService $service
    ) {}

   
    public function publicShow(
        string $slug
    ): AnonymousResourceCollection {
        return FeatureFifthSectionResource::collection(
            $this->service->getBySlug($slug)
        );
    }

   
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        $filters = $request->validate([
            'page_name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        return FeatureFifthSectionResource::collection(
            $this->service->getAll($filters)
        );
    }

  
    public function show(int $id): FeatureFifthSectionResource
    {
        return new FeatureFifthSectionResource(
            $this->service->find($id)
        );
    }

  
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'page_name' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'feature' => ['required', 'array'],
            'feature.*' => ['required', 'string', 'max:255'],
        ]);

        $section = $this->service->create($data);

        return response()->json([
            'message' => 'Feature fifth section created successfully.',
            'data' => new FeatureFifthSectionResource($section),
        ], 201);
    }

    
    public function update(
        Request $request,
        int $id
    ): JsonResponse {
        $section = $this->service->find($id);

        $data = $request->validate([
            'page_name' => ['sometimes', 'required', 'string', 'max:255'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'feature' => ['sometimes', 'required', 'array'],
            'feature.*' => ['required', 'string', 'max:255'],
        ]);

        $section = $this->service->update($section, $data);

        return response()->json([
            'message' => 'Feature fifth section updated successfully.',
            'data' => new FeatureFifthSectionResource($section),
        ]);
    }

   
    public function destroy(int $id): JsonResponse
    {
        $section = $this->service->find($id);

        $this->service->delete($section);

        return response()->json([
            'message' => 'Feature fifth section deleted successfully.',
        ]);
    }
}
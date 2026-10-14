<?php

namespace App\Http\Controllers\Api\V1\LegalPage;

use App\Http\Controllers\Controller;
use App\Models\LegalPage;
use App\Services\LegalPage\LegalPageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LegalPageController extends Controller
{
    public function __construct(
        protected LegalPageService $legalPageService
    ) {}

    // Public: Get all active legal pages
    public function publicIndex(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Legal pages retrieved successfully.',
            'data' => $this->legalPageService->getActivePages(),
        ]);
    }

    // Public: Get an active legal page by slug
    public function publicShow(string $slug): JsonResponse
    {
        $page = $this->legalPageService->getActivePageBySlug($slug);

        return response()->json([
            'success' => true,
            'message' => 'Legal page retrieved successfully.',
            'data' => $page,
        ]);
    }

    // Admin: List legal pages with filters and pagination
    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'page_name' => ['sometimes', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Legal pages retrieved successfully.',
            'data' => $this->legalPageService->getAll($filters),
        ]);
    }

    // Admin: Get a legal page by ID
    public function show(int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Legal page retrieved successfully.',
            'data' => $this->legalPageService->find($id),
        ]);
    }

    // Admin: Create a legal page
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'page_name' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'is_order' => ['sometimes', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $page = $this->legalPageService->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Legal page created successfully.',
            'data' => $page,
        ], 201);
    }

    // Admin: Update a legal page
    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'page_name' => ['sometimes', 'required', 'string', 'max:255'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'is_order' => ['sometimes', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $page = $this->legalPageService->find($id);

        $page = $this->legalPageService->update($page, $data);

        return response()->json([
            'success' => true,
            'message' => 'Legal page updated successfully.',
            'data' => $page,
        ]);
    }

    // Admin: Delete a legal page
    public function destroy(int $id): JsonResponse
    {
        $page = $this->legalPageService->find($id);

        $this->legalPageService->delete($page);

        return response()->json([
            'success' => true,
            'message' => 'Legal page deleted successfully.',
        ]);
    }
}

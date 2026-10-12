<?php

namespace App\Http\Controllers\Api\V1\Faq;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFaqRequest;
use App\Http\Requests\UpdateFaqRequest;
use App\Models\Faq;
use App\Services\Faq\FaqService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function __construct(
        protected FaqService $faqService
    ) {}

    /**
     * Public: Get all active FAQs.
     */
    public function publicIndex(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'FAQs retrieved successfully.',
            'data' => $this->faqService->getActiveFaqs(),
        ]);
    }

    /**
     * Public: Get active FAQs for a page.
     */
    public function publicBySlug(string $slug): JsonResponse
    {
        $faqs = $this->faqService->getActiveFaqsBySlug($slug);

        return response()->json([
            'success' => true,
            'message' => 'FAQs retrieved successfully.',
            'data' => $faqs,
        ]);
    }

    /**
     * Admin: Get paginated FAQs.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'page_name' => ['sometimes', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FAQs retrieved successfully.',
            'data' => $this->faqService->getAll($filters),
        ]);
    }

    /**
     * Admin: Get one FAQ.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'FAQ retrieved successfully.',
            'data' => $this->faqService->find($id),
        ]);
    }

    /**
     * Admin: Create an FAQ.
     */
    public function store(StoreFaqRequest $request): JsonResponse
    {
        $faq = $this->faqService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'FAQ created successfully.',
            'data' => $faq,
        ], 201);
    }

    /**
     * Admin: Update an FAQ.
     */
    public function update(
        UpdateFaqRequest $request,
        int $id
    ): JsonResponse {
        $faq = $this->faqService->find($id);

        $faq = $this->faqService->update(
            $faq,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'FAQ updated successfully.',
            'data' => $faq,
        ]);
    }

    /**
     * Admin: Delete an FAQ.
     */
    public function destroy(int $id): JsonResponse
    {
        $faq = $this->faqService->find($id);

        $this->faqService->delete($faq);

        return response()->json([
            'success' => true,
            'message' => 'FAQ deleted successfully.',
        ]);
    }
}

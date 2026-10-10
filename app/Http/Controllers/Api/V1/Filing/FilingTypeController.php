<?php

namespace App\Http\Controllers\Api\V1\Filing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Filing\StoreFilingTypeRequest;
use App\Http\Requests\Api\V1\Filing\UpdateFilingTypeRequest;
use App\Models\FilingType;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FilingTypeController extends Controller
{
    public function __construct(
        protected ApiResponseService $apiResponse
    ) {}

    /**
     * List filing types.
     */
    public function index(Request $request)
    {
        $filingTypes = FilingType::get();

        // if ($request->filled('category')) {
        //     $query->where('category', $request->category);
        // }

        // if ($request->has('is_active')) {
        //     $query->where(
        //         'is_active',
        //         $request->boolean('is_active')
        //     );
        // }

        // if ($request->filled('search')) {
        //     $search = $request->search;

        //     $query->where(function ($q) use ($search) {
        //         $q->where('name', 'like', "%{$search}%")
        //             ->orWhere('slug', 'like', "%{$search}%")
        //             ->orWhere('authority', 'like', "%{$search}%");
        //     });
        // }

        // $filingTypes = $query
        //     ->orderBy('sort_order')
        //     ->latest('id')
        //     ->paginate(
        //         min(max($request->integer('per_page', 15), 1), 100)
        //     );

        return $this->apiResponse->success(
            'Filing types retrieved successfully.',
            $filingTypes
            
        );
    }

    /**
     * Create filing type.
     */
    public function store(StoreFilingTypeRequest $request)
    {
        $validated = $request->validated();

        $validated['slug'] = $this->generateUniqueSlug(
            $validated['slug'] ?? $validated['name']
        );

        $validated['is_active'] ??= true;
        $validated['sort_order'] ??= 0;

        $filingType = FilingType::create($validated);

        return $this->apiResponse->success(
            'Filing type created successfully.',
            $filingType
            
        );
    }

    /**
     * Show filing type.
     */
    public function show($filingTypeId)
    {
        $filingType = FilingType::with('filings')
            ->find($filingTypeId);

        if (!$filingType) {
            return $this->apiResponse->error(
                'Filing type not found.',
                404
            );
        }

        return $this->apiResponse->success(
            'Filing type retrieved successfully.',    
            $filingType 
        );
    }

    /**
     * Update filing type.
     */
    public function update(
        UpdateFilingTypeRequest $request,
        $filingTypeId
    ) {
        $filingType = FilingType::find($filingTypeId);

        if (!$filingType) {
            return $this->apiResponse->error(
                'Filing type not found.',
                404
            );
        }

        $validated = $request->validated();

        if (
            array_key_exists('slug', $validated)
            && empty($validated['slug'])
        ) {
            $validated['slug'] = $validated['name']
                ?? $filingType->name;
        } elseif (
            !array_key_exists('slug', $validated)
            && isset($validated['name'])
        ) {
            $validated['slug'] = $validated['name'];
        }

        if (array_key_exists('slug', $validated)) {
            $validated['slug'] = $this->generateUniqueSlug(
                $validated['slug'],
                $filingType->id
            );
        }

        $filingType->update($validated);

        return $this->apiResponse->success(
            
            'Filing type updated successfully.',
            $filingType->fresh(),
        );
    }

    /**
     * Delete filing type.
     */
    public function destroy($filingTypeId)
    {
        $filingType = FilingType::find($filingTypeId);

        if (!$filingType) {
            return $this->apiResponse->error(
                'Filing type not found.',
                404
            );
        }

        if ($filingType->filings()->exists()) {
            return $this->apiResponse->error(
                'This filing type is already associated with filings and cannot be deleted.',
                422
            );
        }

        $filingType->delete();

        return $this->apiResponse->noContent();
    }

    /**
     * Generate a unique slug.
     */
    private function generateUniqueSlug(
        string $value,
        ?int $ignoreId = null
    ): string {
        $baseSlug = Str::slug($value);

        if ($baseSlug === '') {
            $baseSlug = 'filing-type';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (
            FilingType::where('slug', $slug)
                ->when(
                    $ignoreId,
                    fn ($query) => $query->where('id', '!=', $ignoreId)
                )
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
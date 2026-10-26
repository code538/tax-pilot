<?php

namespace App\Http\Controllers\Api\V1\Subscription\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Subscription\SubscriptionProductResource;
use App\Models\SubscriptionProduct;
use App\Services\Subscription\SubscriptionProductAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SubscriptionProductController extends Controller
{
    public function __construct(
        protected SubscriptionProductAdminService $service
    ) {
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $products = $this->service->getAll($filters);

        return SubscriptionProductResource::collection($products);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:subscription_products,slug'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $product = $this->service->create($data);

        return (new SubscriptionProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id): SubscriptionProductResource
    {
        return new SubscriptionProductResource(
            $this->service->find($id)
        );
    }

    public function update(Request $request, int $id): SubscriptionProductResource
    {
        $product = SubscriptionProduct::findOrFail($id);

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('subscription_products', 'slug')->ignore($product->id),
            ],
            'description' => ['sometimes', 'nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        return new SubscriptionProductResource(
            $this->service->update($product, $data)
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $product = SubscriptionProduct::findOrFail($id);

        $this->service->delete($product);

        return response()->json([
            'success' => true,
            'message' => 'Subscription product deleted successfully.',
        ]);
    }
}
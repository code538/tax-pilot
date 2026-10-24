<?php

namespace App\Http\Controllers\Api\V1\Subscription;

use App\Http\Controllers\Controller;
use App\Http\Resources\Subscription\SubscriptionProductResource;
use App\Services\Subscription\SubscriptionPricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubscriptionPricingController extends Controller
{
    public function __construct(
        protected SubscriptionPricingService $subscriptionPricingService
    ) {
    }

    /**
     
     * Get all active products and their plans.
     */
    public function index(): AnonymousResourceCollection
    {
        $products = $this->subscriptionPricingService
            ->getActiveProducts();

        return SubscriptionProductResource::collection($products);
    }

    /**
    
     * Get one active product by slug.
     */
    public function show(string $slug): JsonResponse|SubscriptionProductResource
    {
        $product = $this->subscriptionPricingService
            ->getActiveProductBySlug($slug);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription product not found.',
            ], 404);
        }

        return new SubscriptionProductResource($product);
    }
}
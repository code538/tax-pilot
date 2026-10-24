<?php

namespace App\Http\Resources\Subscription;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,

            'prices' => SubscriptionPlanPriceResource::collection(
                $this->whenLoaded('activePrices')
            ),

            'filing_types' => $this->whenLoaded(
                'filingTypes',
                fn () => $this->filingTypes->map(fn ($filingType) => [
                    'id' => $filingType->id,
                    'name' => $filingType->name,
                    'slug' => $filingType->slug,
                    'category' => $filingType->category,
                    'authority' => $filingType->authority,
                ])->values()
            ),

            'entitlements' => SubscriptionPlanEntitlementResource::collection(
                $this->whenLoaded('entitlements')
            ),
        ];
    }
}
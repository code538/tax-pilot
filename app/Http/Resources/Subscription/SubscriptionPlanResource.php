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

            // Pricing card display fields
            'tagline' => $this->tagline,
            'badge_text' => $this->badge_text,
            'button_text' => $this->button_text,
            'display_group' => $this->display_group,

            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,

            // Plan prices
            'prices' => SubscriptionPlanPriceResource::collection(
                $this->whenLoaded('activePrices')
            ),

            // Supported filing types
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

            // Backend permissions and limits
            'entitlements' => SubscriptionPlanEntitlementResource::collection(
                $this->whenLoaded('entitlements')
            ),

            // Features displayed on the pricing card
            'features' => SubscriptionPlanFeatureResource::collection(
                $this->whenLoaded('features')
            ),
        ];
    }
}

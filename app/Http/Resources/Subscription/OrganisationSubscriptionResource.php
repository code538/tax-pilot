<?php

namespace App\Http\Resources\Subscription;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganisationSubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'organisation_id' => $this->organisation_id,

            'status' => $this->status,

            'is_currently_active' => $this->isCurrentlyActive(),

            'starts_at' => $this->starts_at,

            'current_period_starts_at' => $this->current_period_starts_at,

            'current_period_ends_at' => $this->current_period_ends_at,

            'ends_at' => $this->ends_at,

            'cancelled_at' => $this->cancelled_at,

            'billing' => [
                'amount_minor' => $this->amount_minor,
                'currency' => $this->currency,
                'billing_interval' => $this->billing_interval,
            ],

            'plan' => $this->whenLoaded('plan', function () {
                if (! $this->plan) {
                    return null;
                }

                return [
                    'id' => $this->plan->id,
                    'name' => $this->plan->name,
                    'slug' => $this->plan->slug,
                    'description' => $this->plan->description,
                ];
            }),

            'price' => $this->whenLoaded('price', function () {
                if (! $this->price) {
                    return null;
                }

                return [
                    'id' => $this->price->id,
                    'billing_interval' => $this->price->billing_interval,
                    'interval_count' => $this->price->interval_count,
                    'amount_minor' => $this->price->amount_minor,
                    'currency' => $this->price->currency,
                    'formatted_price' => $this->price->formatted_price,
                ];
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
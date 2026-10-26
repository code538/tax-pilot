<?php

namespace App\Http\Resources\Subscription;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanPriceAdminResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subscription_plan_id' => $this->subscription_plan_id,

            'billing_interval' => $this->billing_interval,
            'interval_count' => $this->interval_count,

            'amount_minor' => $this->amount_minor,
            'currency' => $this->currency,
            'formatted_price' => $this->formatted_price,

            'stripe_product_id' => $this->stripe_product_id,
            'stripe_price_id' => $this->stripe_price_id,

            'is_active' => $this->is_active,
            'effective_from' => $this->effective_from,
            'effective_until' => $this->effective_until,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

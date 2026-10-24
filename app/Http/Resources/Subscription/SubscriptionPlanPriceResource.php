<?php

namespace App\Http\Resources\Subscription;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanPriceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'billing_interval' => $this->billing_interval,
            'interval_count' => $this->interval_count,
            'amount_minor' => $this->amount_minor,
            'currency' => $this->currency,

            'formatted_price' => $this->currency . ' ' . number_format(
                $this->amount_minor / 100,
                2
            ),

            'is_active' => $this->is_active,
            'effective_from' => $this->effective_from,
            'effective_until' => $this->effective_until,
        ];
    }
}
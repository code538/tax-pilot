<?php

namespace App\Http\Resources\Subscription;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanEntitlementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'key' => $this->entitlement_key,
            'type' => $this->value_type,
            'value' => $this->value,
        ];
    }
}
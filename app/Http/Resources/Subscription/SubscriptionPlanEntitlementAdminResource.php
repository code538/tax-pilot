<?php

namespace App\Http\Resources\Subscription;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanEntitlementAdminResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subscription_plan_id' => $this->subscription_plan_id,
            'entitlement_key' => $this->entitlement_key,
            'value_type' => $this->value_type,
            'value' => $this->value,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
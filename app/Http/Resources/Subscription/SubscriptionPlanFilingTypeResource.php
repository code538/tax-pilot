<?php

namespace App\Http\Resources\Subscription;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanFilingTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subscription_plan_id' => $this->subscription_plan_id,
            'filing_type_id' => $this->filing_type_id,

            'filing_type' => $this->whenLoaded(
                'filingType',
                function () {
                    return [
                        'id' => $this->filingType->id,
                        'name' => $this->filingType->name,
                        'slug' => $this->filingType->slug,
                    ];
                }
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
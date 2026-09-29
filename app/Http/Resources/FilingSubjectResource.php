<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FilingSubjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'subject_type' => $this->subject_type,

            'name' => $this->name,

            'company_number' => $this->company_number,
            'company_type' => $this->company_type,

            'utr_number' => $this->utr_number,
            'vat_number' => $this->vat_number,

            'email' => $this->email,
            'phone' => $this->phone,

            'address' => [
                'line_1' => $this->address_line_1,
                'line_2' => $this->address_line_2,
                'city' => $this->city,
                'county' => $this->county,
                'postcode' => $this->postcode,
                'country' => $this->country,
            ],

            'companies_house' => [
                'status' => $this->companies_house_status,
                'incorporation_date' => $this->incorporation_date?->format('Y-m-d'),
                'dissolution_date' => $this->dissolution_date?->format('Y-m-d'),
            ],

            'status' => $this->status,

            'notes' => $this->notes,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
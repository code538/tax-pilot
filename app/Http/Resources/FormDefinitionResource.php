<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormDefinitionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'filing_type_id' => $this->filing_type_id,
            'filing_type' => $this->whenLoaded(
                'filingType',
                fn () => [
                    'id' => $this->filingType->id,
                    'name' => $this->filingType->name,
                    'slug' => $this->filingType->slug,
                ]
            ),
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormVersionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'form_definition_id' => $this->form_definition_id,
            'version_number' => $this->version_number,
            'status' => $this->status,
            'published_at' => $this->published_at?->toISOString(),

            'created_by' => $this->whenLoaded(
                'createdBy',
                fn () => [
                    'id' => $this->createdBy?->id,
                    'name' => $this->createdBy?->name,
                    'email' => $this->createdBy?->email,
                ]
            ),

            'form_definition' => $this->whenLoaded(
                'formDefinition',
                fn () => [
                    'id' => $this->formDefinition->id,
                    'name' => $this->formDefinition->name,
                    'slug' => $this->formDefinition->slug,
                ]
            ),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
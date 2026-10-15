<?php

namespace App\Http\Resources\WebsiteContent;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeatureSecondSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'page_title' => $this->page_title,
            'slug' => $this->slug,
            'title' => $this->title,
            'heading' => $this->heading,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

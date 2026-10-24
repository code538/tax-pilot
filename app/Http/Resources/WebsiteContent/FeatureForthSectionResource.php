<?php

namespace App\Http\Resources\WebsiteContent;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeatureForthSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'page_name' => $this->page_name,
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'feature' => $this->feature ?? [],
            'mobile_image' => $this->mobile_image,
            'web_image' => $this->web_image,
            'image_alt' => $this->image_alt,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
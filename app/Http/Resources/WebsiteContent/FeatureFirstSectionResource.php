<?php

namespace App\Http\Resources\WebsiteContent;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeatureFirstSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'page_title' => $this->page_title,
            'slug' => $this->slug,
            'batch' => $this->batch,
            'heading' => $this->heading,
            'heading_meta' => $this->heading_meta,
            'description' => $this->description,
            'desc_meta' => $this->desc_meta,
            'primary_button' => $this->primary_button,
            'primary_button_url' => $this->primary_button_url,
            'secondary_button' => $this->secondary_button,
            'secondary_button_url' => $this->secondary_button_url,
            'trust_text' => $this->trust_text,
            'web_image' => $this->web_image,
            'mobile_image' => $this->mobile_image,
            'image_alt' => $this->image_alt,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

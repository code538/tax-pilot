<?php

namespace App\Http\Resources\WebsiteContent;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeatureSeventhSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'page_title' => $this->page_title,
            'slug' => $this->slug,
            'section_title' => $this->section_title,
            'section_desc' => $this->section_desc,
            'icon' => $this->icon,
            'title' => $this->title,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FilingVersionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'filing_id' => $this->filing_id,

            'version_number' => $this->version_number,

            'supersedes_version_id' => $this->supersedes_version_id,

            'revision_reason' => $this->revision_reason,

            'prepared_by' => $this->whenLoaded(
                'preparedBy',
                function () {
                    return [
                        'id' => $this->preparedBy?->id,
                        'name' => $this->preparedBy?->name,
                        'email' => $this->preparedBy?->email,
                    ];
                }
            ),

            'prepared_at' => $this->prepared_at?->toISOString(),

            'locked_at' => $this->locked_at?->toISOString(),

            'content_hash' => $this->content_hash,

            'created_at' => $this->created_at?->toISOString(),

            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
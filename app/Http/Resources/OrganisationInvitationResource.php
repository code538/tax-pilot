<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganisationInvitationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'email' => $this->email,

            'status' => $this->status,

            'expires_at' => $this->expires_at,

            'accepted_at' => $this->accepted_at,

            'cancelled_at' => $this->cancelled_at,

            'role' => $this->whenLoaded(
                'role',
                function () {
                    return [
                        'id' => $this->role->id,
                        'name' => $this->role->name,
                        'slug' => $this->role->slug,
                    ];
                }
            ),

            'invited_by' => $this->whenLoaded(
                'inviter',
                function () {
                    return [
                        'id' => $this->inviter->id,
                        'name' => $this->inviter->name,
                        'email' => $this->inviter->email,
                    ];
                }
            ),
        ];
    }
}
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_system' => $this->is_system,
            'is_active' => $this->is_active,

            'permissions' => $this->whenLoaded(
                'permissions',
                function () {
                    return $this->permissions->map(
                        function ($permission) {
                            return [
                                'id' => $permission->id,
                                'name' => $permission->name,
                                'slug' => $permission->slug,
                                'module' => $permission->module,
                                'action' => $permission->action,
                            ];
                        }
                    )->values();
                }
            ),
        ];
    }
}
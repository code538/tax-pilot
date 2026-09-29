<?php

namespace App\Http\Controllers\Api\V1\Organisation;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Services\ApiResponseService;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    public function index(): JsonResponse
    {
        $permissions = Permission::query()
            ->where('is_active', true)
            ->orderBy('module')
            ->orderBy('action')
            ->get([
                'id',
                'name',
                'slug',
                'module',
                'action',
                'description',
            ]);

        return app(ApiResponseService::class)->success(
            'Permissions retrieved successfully.',
            $permissions
        );
    }
}
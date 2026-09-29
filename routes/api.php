<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Organisation\OrganisationController;
use App\Http\Controllers\Api\V1\Admin\SuperAdminController;
use App\Http\Controllers\Api\V1\Organisation\OrganisationUserController;
use App\Http\Controllers\Api\V1\Organisation\RoleController;
use App\Http\Controllers\Api\V1\Organisation\PermissionController;
use App\Http\Controllers\Api\V1\Organisation\OrganisationInvitationController;
use App\Http\Controllers\Api\V1\Auth\InvitationController;
use App\Http\Controllers\Api\V1\FilingSubject\FilingSubjectController;



Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {

        Route::post(
            'register',
            [AuthController::class, 'register']
        );

        Route::post(
            'login',
            [AuthController::class, 'login']
        );

        Route::middleware('auth:sanctum')->group(function () {

            Route::post(
                'logout',
                [AuthController::class, 'logout']
            );

            Route::get(
                'me',
                [AuthController::class, 'me']
            );

            Route::get(
                'my-organisations',
                [OrganisationController::class, 'myOrganisations']
            );
        });


        
    });

    // Route::middleware([
    //     'auth:sanctum',
    //     'organisation',
    // ])->prefix('organisations/{organisationId}')->group(function () {

    //     Route::get(
    //         '/',
    //         [OrganisationController::class, 'show']
    //     );

    //     Route::put(
    //         '/',
    //         [OrganisationController::class, 'update']
    //     );

    //     Route::delete(
    //         '/',
    //         [OrganisationController::class, 'destroy']
    //     );
    // });
    Route::middleware([
        'auth:sanctum',
        'organisation',
    ])->prefix('organisations/{organisationId}')->group(function () {

        Route::get(
            '/',
            [OrganisationController::class, 'show']
        )->middleware('permission:organisation.view');

        Route::put(
            '/',
            [OrganisationController::class, 'update']
        )->middleware('permission:organisation.update');

        Route::delete(
            '/',
            [OrganisationController::class, 'destroy']
        )->middleware('permission:organisation.update');
    });

    Route::middleware([
        'auth:sanctum',
        'organisation',
    ])->prefix('organisations/{organisationId}/users')->group(function () {

        Route::get(
            '/',
            [OrganisationUserController::class, 'index']
        )->middleware('permission:user.view');

        Route::post(
            '/',
            [OrganisationUserController::class, 'store']
        )->middleware('permission:user.create');

        Route::get(
            '/{userId}',
            [OrganisationUserController::class, 'show']
        )->middleware('permission:user.view');

        Route::put(
            '/{userId}',
            [OrganisationUserController::class, 'update']
        )->middleware('permission:user.update');

        Route::delete(
            '/{userId}',
            [OrganisationUserController::class, 'destroy']
        )->middleware('permission:user.delete');
    });
    // testing permission middleware
    Route::middleware([
        'auth:sanctum',
        'organisation',
        'permission:billing.manage',
    ])->get(
        'test-billing-permission/{organisationId}',
        function () {
            return response()->json([
                'success' => true,
                'message' => 'You have billing.manage permission.',
            ]);
        }
    );

    Route::middleware([
        'auth:sanctum',
        'super.admin',
    ])->prefix('admin')->group(function () {

        Route::get(
            'organisations',
            [SuperAdminController::class, 'organisations']
        );

        Route::get(
            'organisations/{organisationId}',
            [SuperAdminController::class, 'organisation']
        );

        Route::delete(
            'organisations/{organisationId}',
            [SuperAdminController::class, 'deleteOrganisation']
        );
    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::get(
            'permissions',
            [PermissionController::class, 'index']
        );
    });

    Route::middleware([
    'auth:sanctum',
    'organisation',
    ])->prefix('organisations/{organisationId}/roles')->group(function () {

        Route::get(
            '/',
            [RoleController::class, 'index']
        )->middleware('permission:role.view');

        Route::post(
            '/',
            [RoleController::class, 'store']
        )->middleware('permission:role.create');

        Route::get(
            '/{roleId}',
            [RoleController::class, 'show']
        )->middleware('permission:role.view');

        Route::put(
            '/{roleId}',
            [RoleController::class, 'update']
        )->middleware('permission:role.update');

        Route::delete(
            '/{roleId}',
            [RoleController::class, 'destroy']
        )->middleware('permission:role.delete');
    });

    Route::middleware([
        'auth:sanctum',
        'organisation',
    ])->prefix(
        'organisations/{organisationId}/invitations'
    )->group(function () {

        Route::get(
            '/',
            [OrganisationInvitationController::class, 'index']
        )->middleware('permission:user.invite');

        Route::post(
            '/',
            [OrganisationInvitationController::class, 'store']
        )->middleware('permission:user.invite');

        Route::post(
            '/{invitationId}/cancel',
            [OrganisationInvitationController::class, 'cancel']
        )->middleware('permission:user.invite');
    });

    Route::middleware(['auth:sanctum', 'organisation'])
        ->prefix('organisations/{organisationId}/filing-subjects')
        ->group(function () {

            Route::get(
                '/',
                [FilingSubjectController::class, 'index']
            )->middleware('permission:filing_subject.view');

            Route::post(
                '/',
                [FilingSubjectController::class, 'store']
            )->middleware('permission:filing_subject.create');

            Route::get(
                '/{subjectId}',
                [FilingSubjectController::class, 'show']
            )->middleware('permission:filing_subject.view');

            Route::put(
                '/{subjectId}',
                [FilingSubjectController::class, 'update']
            )->middleware('permission:filing_subject.update');

            Route::delete(
                '/{subjectId}',
                [FilingSubjectController::class, 'destroy']
            )->middleware('permission:filing_subject.delete');
    });

    Route::post('invitations/{token}/accept',
        [InvitationController::class, 'accept']
    );

});
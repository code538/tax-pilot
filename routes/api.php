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
use App\Http\Controllers\Api\V1\Filing\FilingController;
use App\Http\Controllers\Api\V1\Filing\FilingTypeController;
use App\Http\Controllers\Api\V1\Filing\FilingVersionController;
use App\Http\Controllers\Api\V1\FormDefinitionController;
use App\Http\Controllers\Api\V1\FormVersionController;
use App\Http\Controllers\Api\V1\FormSectionController;
use App\Http\Controllers\Api\V1\FormFieldController;
use App\Http\Controllers\Api\V1\FilingFormController;
use App\Http\Controllers\Api\V1\Filing\FilingFieldValueController;



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

    Route::middleware([
    'auth:sanctum',
    'organisation',
    ])->prefix('organisations/{organisationId}')->group(function () {

        Route::get(
            'filings/{filingId}/form',
            [FilingFormController::class, 'show']
        )
            ->whereNumber('organisationId')
            ->whereNumber('filingId');

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

        //dynamic form field add
        Route::get(
            'form-versions/{formVersionId}/sections/{formSectionId}/fields',
            [FormFieldController::class, 'index']
        )->whereNumber('formVersionId')
         ->whereNumber('formSectionId');

        Route::post(
            'form-versions/{formVersionId}/sections/{formSectionId}/fields',
            [FormFieldController::class, 'store']
        )->whereNumber('formVersionId')
         ->whereNumber('formSectionId');

        Route::get(
            'form-versions/{formVersionId}/sections/{formSectionId}/fields/{formFieldId}',
            [FormFieldController::class, 'show']
        )->whereNumber('formVersionId')
         ->whereNumber('formSectionId')
         ->whereNumber('formFieldId');

        Route::match(
            ['put', 'patch'],
            'form-versions/{formVersionId}/sections/{formSectionId}/fields/{formFieldId}',
            [FormFieldController::class, 'update']
        )->whereNumber('formVersionId')
         ->whereNumber('formSectionId')
         ->whereNumber('formFieldId');

        Route::delete(
            'form-versions/{formVersionId}/sections/{formSectionId}/fields/{formFieldId}',
            [FormFieldController::class, 'destroy']
        )->whereNumber('formVersionId')
         ->whereNumber('formSectionId')
         ->whereNumber('formFieldId');

        

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

    Route::middleware([
        'auth:sanctum',
        'organisation',
    ])->prefix(
        'organisations/{organisationId}/filings'
    )->group(function () {

        Route::get(
            '/',
            [FilingController::class, 'index']
        )->middleware('permission:filing.view');

        Route::post(
            '/',
            [FilingController::class, 'store']
        )->middleware('permission:filing.create');

        Route::get(
            '/{filingId}',
            [FilingController::class, 'show']
        )->middleware('permission:filing.view');

        Route::put(
            '/{filingId}',
            [FilingController::class, 'update']
        )->middleware('permission:filing.update');

        Route::delete(
            '/{filingId}',
            [FilingController::class, 'destroy']
        )->middleware('permission:filing.delete');
    });

    Route::middleware([
        'auth:sanctum',
        'organisation',
    ])
        ->prefix('organisations/{organisationId}')
        ->group(function () {

            Route::prefix('filings/{filingId}/versions')
                ->group(function () {

                    Route::get(
                        '/',
                        [FilingVersionController::class, 'index']
                    );

                    Route::post(
                        '/',
                        [FilingVersionController::class, 'store']
                    );

                    Route::get(
                        '/{versionId}',
                        [FilingVersionController::class, 'show']
                    );
                });
        });

    
    Route::middleware('auth:sanctum')
        ->group(function () {
            Route::prefix('form-definitions')
                ->name('form-definitions.')
                ->group(function () {
                    Route::get(
                        '/',
                        [FormDefinitionController::class, 'index']
                    )->name('index');

                    Route::post(
                        '/',
                        [FormDefinitionController::class, 'store']
                    )->name('store');

                    Route::get(
                        '/{formDefinitionId}',
                        [FormDefinitionController::class, 'show']
                    )->whereNumber('formDefinitionId')
                    ->name('show');

                    Route::put(
                        '/{formDefinitionId}',
                        [FormDefinitionController::class, 'update']
                    )->whereNumber('formDefinitionId')
                    ->name('update');

                    Route::delete(
                        '/{formDefinitionId}',
                        [FormDefinitionController::class, 'destroy']
                    )->whereNumber('formDefinitionId')
                    ->name('destroy');
                });
        });

    Route::middleware(['auth:sanctum'])->prefix('form-versions')->group(function () {

        Route::get(
            '/',
            [FormVersionController::class, 'index']
        );

        Route::post(
            '/',
            [FormVersionController::class, 'store']
        );

        Route::get(
            '/{formVersionId}',
            [FormVersionController::class, 'show']
        )->whereNumber('formVersionId');

        Route::post(
            '/{formVersionId}/publish',
            [FormVersionController::class, 'publish']
        )->whereNumber('formVersionId');

        Route::delete(
            '/versions/{formVersionId}',
            [FormVersionController::class, 'destroy']
        )->whereNumber('formVersionId');

        //form sections routes
        Route::get(
        '/{formVersionId}/sections',
            [FormSectionController::class, 'index']
        )->whereNumber('formVersionId');

        Route::post(
            '/{formVersionId}/sections',
            [FormSectionController::class, 'store']
        )->whereNumber('formVersionId');

        Route::get(
            '/{formVersionId}/sections/{formSectionId}',
            [FormSectionController::class, 'show']
        )->whereNumber('formVersionId')
         ->whereNumber('formSectionId');

        Route::put(
            '/{formVersionId}/sections/{formSectionId}',
            [FormSectionController::class, 'update']
        )->whereNumber('formVersionId')
         ->whereNumber('formSectionId');

        // Route::patch(
        //     '/{formVersionId}/sections/{formSectionId}',
        //     [FormSectionController::class, 'update']
        // )->whereNumber('formVersionId')
        //  ->whereNumber('formSectionId');

        Route::delete(
            '/{formVersionId}/sections/{formSectionId}',
            [FormSectionController::class, 'destroy']
        )->whereNumber('formVersionId')
         ->whereNumber('formSectionId');

    });  

    Route::middleware([
        'auth:sanctum',
        'organisation',
    ])
        ->prefix('organisations/{organisationId}')
        ->group(function () {

            Route::get(
                'filings/{filingId}/versions/{filingVersionId}/values',
                [FilingFieldValueController::class, 'index']
            );

            Route::post(
                'filings/{filingId}/versions/{filingVersionId}/values',
                [FilingFieldValueController::class, 'store']
            );
        });

        Route::prefix('filing-types')->group(function () {

            Route::get('/', [
                FilingTypeController::class,
                'index',
            ]);

            Route::post('/', [
                FilingTypeController::class,
                'store',
            ]);

            Route::get('/{filingTypeId}', [
                FilingTypeController::class,
                'show',
            ]);

            Route::put('/{filingTypeId}', [
                FilingTypeController::class,
                'update',
            ]);

            Route::patch('/{filingTypeId}', [
                FilingTypeController::class,
                'update',
            ]);

            Route::delete('/{filingTypeId}', [
                FilingTypeController::class,
                'destroy',
            ]);
        });
    
    
  

    Route::post('invitations/{token}/accept',
        [InvitationController::class, 'accept']
    );

});
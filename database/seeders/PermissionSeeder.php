<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            /*
            |--------------------------------------------------------------------------
            | Organisation
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'View Organisation',
                'slug' => 'organisation.view',
                'module' => 'organisation',
                'action' => 'view',
                'description' => 'View organisation information.',
            ],
            [
                'name' => 'Update Organisation',
                'slug' => 'organisation.update',
                'module' => 'organisation',
                'action' => 'update',
                'description' => 'Update organisation information.',
            ],

            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'View Users',
                'slug' => 'user.view',
                'module' => 'user',
                'action' => 'view',
                'description' => 'View organisation users.',
            ],
            [
                'name' => 'Create User',
                'slug' => 'user.create',
                'module' => 'user',
                'action' => 'create',
                'description' => 'Create users.',
            ],
            [
                'name' => 'Update User',
                'slug' => 'user.update',
                'module' => 'user',
                'action' => 'update',
                'description' => 'Update users.',
            ],
            [
                'name' => 'Delete User',
                'slug' => 'user.delete',
                'module' => 'user',
                'action' => 'delete',
                'description' => 'Delete users.',
            ],
            [
                'name' => 'Invite User',
                'slug' => 'user.invite',
                'module' => 'user',
                'action' => 'invite',
                'description' => 'Invite users to the organisation.',
            ],

            /*
            |--------------------------------------------------------------------------
            | Roles
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'View Roles',
                'slug' => 'role.view',
                'module' => 'role',
                'action' => 'view',
                'description' => 'View roles.',
            ],
            [
                'name' => 'Create Role',
                'slug' => 'role.create',
                'module' => 'role',
                'action' => 'create',
                'description' => 'Create roles.',
            ],
            [
                'name' => 'Update Role',
                'slug' => 'role.update',
                'module' => 'role',
                'action' => 'update',
                'description' => 'Update roles.',
            ],
            [
                'name' => 'Delete Role',
                'slug' => 'role.delete',
                'module' => 'role',
                'action' => 'delete',
                'description' => 'Delete roles.',
            ],

            /*
            |--------------------------------------------------------------------------
            | Companies
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'View Companies',
                'slug' => 'company.view',
                'module' => 'company',
                'action' => 'view',
                'description' => 'View client companies.',
            ],
            [
                'name' => 'Create Company',
                'slug' => 'company.create',
                'module' => 'company',
                'action' => 'create',
                'description' => 'Create client companies.',
            ],
            [
                'name' => 'Update Company',
                'slug' => 'company.update',
                'module' => 'company',
                'action' => 'update',
                'description' => 'Update client companies.',
            ],
            [
                'name' => 'Delete Company',
                'slug' => 'company.delete',
                'module' => 'company',
                'action' => 'delete',
                'description' => 'Delete client companies.',
            ],

            /*
            |--------------------------------------------------------------------------
            | Filings
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'View Filings',
                'slug' => 'filing.view',
                'module' => 'filing',
                'action' => 'view',
                'description' => 'View tax filings.',
            ],
            [
                'name' => 'Create Filing',
                'slug' => 'filing.create',
                'module' => 'filing',
                'action' => 'create',
                'description' => 'Create tax filings.',
            ],
            [
                'name' => 'Update Filing',
                'slug' => 'filing.update',
                'module' => 'filing',
                'action' => 'update',
                'description' => 'Update tax filings.',
            ],
            [
                'name' => 'Delete Filing',
                'slug' => 'filing.delete',
                'module' => 'filing',
                'action' => 'delete',
                'description' => 'Delete tax filings.',
            ],
            [
                'name' => 'Submit Filing',
                'slug' => 'filing.submit',
                'module' => 'filing',
                'action' => 'submit',
                'description' => 'Submit tax filings to the appropriate authority.',
            ],

            /*
            |--------------------------------------------------------------------------
            | Documents
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'View Documents',
                'slug' => 'document.view',
                'module' => 'document',
                'action' => 'view',
                'description' => 'View documents.',
            ],
            [
                'name' => 'Upload Document',
                'slug' => 'document.upload',
                'module' => 'document',
                'action' => 'upload',
                'description' => 'Upload documents.',
            ],
            [
                'name' => 'Download Document',
                'slug' => 'document.download',
                'module' => 'document',
                'action' => 'download',
                'description' => 'Download documents.',
            ],
            [
                'name' => 'Delete Document',
                'slug' => 'document.delete',
                'module' => 'document',
                'action' => 'delete',
                'description' => 'Delete documents.',
            ],

            /*
            |--------------------------------------------------------------------------
            | Submissions
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'View Submissions',
                'slug' => 'submission.view',
                'module' => 'submission',
                'action' => 'view',
                'description' => 'View external submissions.',
            ],
            [
                'name' => 'Create Submission',
                'slug' => 'submission.create',
                'module' => 'submission',
                'action' => 'create',
                'description' => 'Create a submission.',
            ],
            [
                'name' => 'Retry Submission',
                'slug' => 'submission.retry',
                'module' => 'submission',
                'action' => 'retry',
                'description' => 'Retry failed submissions.',
            ],

            /*
            |--------------------------------------------------------------------------
            | Billing
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'View Billing',
                'slug' => 'billing.view',
                'module' => 'billing',
                'action' => 'view',
                'description' => 'View billing information.',
            ],
            [
                'name' => 'Manage Billing',
                'slug' => 'billing.manage',
                'module' => 'billing',
                'action' => 'manage',
                'description' => 'Manage organisation billing.',
            ],
            [
                'name' => 'Filing Subject View',
                'slug' => 'filing_subject.view',
                'module' => 'filing_subject',
                'action' => 'view',
                'description' => 'View filing subjects.',
                'is_active' => true,
            ],
            [
                'name' => 'Filing Subject Create',
                'slug' => 'filing_subject.create',
                'module' => 'filing_subject',
                'action' => 'create',
                'description' => 'Create filing subjects.',
                'is_active' => true,
            ],
            [
                'name' => 'Filing Subject Update',
                'slug' => 'filing_subject.update',
                'module' => 'filing_subject',
                'action' => 'update',
                'description' => 'Update filing subjects.',
                'is_active' => true,
            ],
            [
                'name' => 'Filing Subject Delete',
                'slug' => 'filing_subject.delete',
                'module' => 'filing_subject',
                'action' => 'delete',
                'description' => 'Delete filing subjects.',
                'is_active' => true,
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                [
                    'slug' => $permission['slug'],
                ],
                [
                    'name' => $permission['name'],
                    'module' => $permission['module'],
                    'action' => $permission['action'],
                    'description' => $permission['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
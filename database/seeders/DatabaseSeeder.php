<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * Global permissions
         */
        $this->call([
            PermissionSeeder::class,
            FilingTypeSeeder::class,
            SuperAdminSeeder::class,
        ]);

        /*
         * Organisation-specific roles are created
         * when an organisation is registered.
         */
    }
}
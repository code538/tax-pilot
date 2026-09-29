<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organisation_id')
                ->constrained('organisations')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('slug');

            $table->text('description')->nullable();

            /*
             * System roles cannot normally be deleted/modified
             * by organisation administrators.
             */
            $table->boolean('is_system')->default(false);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            /*
             * Explicit short unique name.
             * Avoids MySQL's long auto-generated index names.
             */
            $table->unique(
                ['organisation_id', 'slug'],
                'role_org_slug_unq'
            );

            $table->index(
                ['organisation_id', 'is_active'],
                'role_org_active_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
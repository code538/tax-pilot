<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('role_id')
                ->constrained('roles')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('organisation_id')
                ->constrained('organisations')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                ['role_id', 'user_id', 'organisation_id'],
                'role_user_org_unq'
            );

            $table->index(
                ['user_id', 'organisation_id'],
                'user_org_role_idx'
            );

            $table->index(
                ['organisation_id', 'role_id'],
                'org_role_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
    }
};
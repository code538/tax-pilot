<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organisation_users', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organisation_id')
                ->constrained('organisations')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('status', 30)->default('active');

            $table->timestamp('joined_at')->nullable();

            $table->timestamp('last_active_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['organisation_id', 'user_id'],
                'org_user_unq'
            );

            $table->index(
                ['organisation_id', 'status'],
                'org_user_status_idx'
            );

            $table->index(
                ['user_id', 'status'],
                'user_org_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organisation_users');
    }
};
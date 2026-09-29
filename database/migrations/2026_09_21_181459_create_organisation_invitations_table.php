<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organisation_invitations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organisation_id')
                ->constrained('organisations')
                ->cascadeOnDelete();

            $table->foreignId('invited_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('role_id')
                ->nullable()
                ->constrained('roles')
                ->nullOnDelete();

            $table->string('email');
            $table->string('token_hash', 64)->unique();

            $table->string('status', 30)->default('pending');

            $table->timestamp('expires_at');

            $table->timestamp('accepted_at')->nullable();

            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();

            $table->index(
                ['organisation_id', 'email'],
                'org_invite_email_idx'
            );

            $table->index(
                ['organisation_id', 'status'],
                'org_invite_status_idx'
            );

            $table->index(
                ['expires_at', 'status'],
                'invite_exp_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organisation_invitations');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();

            $table->string('module', 100);

            $table->string('action', 50);

            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('module', 'permission_module_idx');
            $table->index('action', 'permission_action_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
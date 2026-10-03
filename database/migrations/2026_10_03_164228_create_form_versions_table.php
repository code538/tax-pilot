<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_versions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('form_definition_id')
                ->constrained('form_definitions')
                ->restrictOnDelete();

            $table->unsignedInteger('version_number');

            $table->string('status', 20)->default('draft');

            $table->timestamp('published_at')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(
                ['form_definition_id', 'version_number'],
                'form_ver_def_num_unq'
            );

            $table->index(
                ['form_definition_id', 'status'],
                'form_ver_def_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_versions');
    }
};
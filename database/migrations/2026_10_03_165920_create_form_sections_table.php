<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_sections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('form_version_id')
                ->constrained('form_versions')
                ->cascadeOnDelete();

            $table->string('name', 150);
            $table->string('slug', 100);
            $table->text('description')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_repeatable')->default(false);

            $table->timestamps();

            $table->unique(
                ['form_version_id', 'slug'],
                'form_sec_ver_slug_unq'
            );

            $table->index(
                ['form_version_id', 'sort_order'],
                'form_sec_ver_sort_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_sections');
    }
};
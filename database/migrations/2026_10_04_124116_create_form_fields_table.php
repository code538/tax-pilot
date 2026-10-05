<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();

            $table->foreignId('form_section_id')
                ->constrained('form_sections')
                ->cascadeOnDelete();

            $table->string('field_key', 100);
            $table->string('label', 200);
            $table->string('field_type', 30);
            $table->string('placeholder')->nullable();
            $table->text('help_text')->nullable();

            $table->boolean('is_required')->default(false);
            $table->json('options')->nullable();
            $table->json('validation_rules')->nullable();

            // Maps this field to an authority-specific payload path.
            $table->string('external_path', 255)->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(
                ['form_section_id', 'field_key'],
                'form_fld_sec_key_unq'
            );

            $table->index(
                ['form_section_id', 'sort_order'],
                'form_fld_sec_sort_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filing_field_values', function (Blueprint $table) {
            $table->id();

            $table->foreignId('filing_id')
                ->constrained('filings')
                ->cascadeOnDelete();

            $table->foreignId('filing_version_id')
                ->constrained('filing_versions')
                ->cascadeOnDelete();

            $table->foreignId('form_field_id')
                ->constrained('form_fields')
                ->restrictOnDelete();

            $table->longText('value')->nullable();

            $table->timestamps();

            $table->unique(
                ['filing_version_id', 'form_field_id'],
                'ffv_version_field_unq'
            );

            $table->index(
                ['filing_id', 'filing_version_id'],
                'ffv_filing_version_idx'
            );

            $table->index(
                ['form_field_id'],
                'ffv_field_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filing_field_values');
    }
};
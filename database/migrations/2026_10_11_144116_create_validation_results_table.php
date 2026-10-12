<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validation_results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('filing_id')
                ->constrained('filings')
                ->cascadeOnDelete();

            $table->foreignId('filing_version_id')
                ->constrained('filing_versions')
                ->cascadeOnDelete();

            $table->string('status', 20)->default('failed');

            $table->unsignedInteger('error_count')->default(0);
            $table->unsignedInteger('warning_count')->default(0);

            $table->json('errors')->nullable();
            $table->json('warnings')->nullable();

            $table->foreignId('validated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('validated_at')->nullable();

            $table->timestamps();

            $table->index(
                ['filing_id', 'filing_version_id'],
                'val_res_filing_ver_idx'
            );

            $table->index(
                ['status', 'validated_at'],
                'val_res_status_date_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validation_results');
    }
};
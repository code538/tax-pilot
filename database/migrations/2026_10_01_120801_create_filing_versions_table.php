<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filing_versions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('filing_id')
                ->constrained('filings')
                ->cascadeOnDelete();

            $table->foreignId('organisation_id')
                ->constrained('organisations')
                ->cascadeOnDelete();

            $table->unsignedInteger('version_number')->default(1);

            $table->foreignId('supersedes_version_id')
                ->nullable()
                ->constrained('filing_versions')
                ->nullOnDelete();

            $table->string('revision_reason', 255)->nullable();

            $table->foreignId('prepared_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('prepared_at')->nullable();

            $table->timestamp('locked_at')->nullable();

            $table->string('content_hash', 128)->nullable();

            $table->timestamps();

            $table->unique(
                ['filing_id', 'version_number'],
                'fil_ver_filing_ver_unq'
            );

            $table->index(
                ['organisation_id', 'filing_id'],
                'fil_ver_org_filing_idx'
            );

            $table->index(
                ['filing_id', 'supersedes_version_id'],
                'fil_ver_supersedes_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filing_versions');
    }
};
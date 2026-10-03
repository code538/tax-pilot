<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organisation_id')
                ->constrained('organisations')
                ->cascadeOnDelete();

            $table->foreignId('filing_subject_id')
                ->constrained('filing_subjects')
                ->cascadeOnDelete();

            $table->foreignId('filing_type_id')
                ->constrained('filing_types')
                ->restrictOnDelete();

            $table->string('reference')->unique();

            $table->string('status')->default('draft');
            //

            $table->foreignId('prepared_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index([
                'organisation_id',
                'filing_subject_id',
            ]);

            $table->index([
                'filing_type_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filings');
    }
};
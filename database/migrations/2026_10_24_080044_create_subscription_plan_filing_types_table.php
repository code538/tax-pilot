<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plan_filing_types', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subscription_plan_id')
                ->constrained('subscription_plans')
                ->cascadeOnDelete();

            $table->foreignId('filing_type_id')
                ->constrained('filing_types')
                ->restrictOnDelete();

            $table->timestamps();

            $table->unique([
                'subscription_plan_id',
                'filing_type_id',
            ], 'plan_filing_type_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plan_filing_types');
    }
};
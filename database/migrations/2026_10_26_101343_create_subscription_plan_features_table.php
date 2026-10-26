<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plan_features', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subscription_plan_id')
                ->constrained('subscription_plans')
                ->cascadeOnDelete();

            $table->string('label', 255);
            $table->boolean('is_included')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(
                ['subscription_plan_id', 'sort_order'],
                'subscription_plan_features_order_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plan_features');
    }
};
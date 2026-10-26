<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subscription_product_id')
                ->constrained('subscription_products')
                ->restrictOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);

            $table->unsignedInteger('sort_order')->default(0);
            

            $table->timestamps();

            $table->index([
                'subscription_product_id',
                'is_active',
            ], 'subscription_plan_product_active_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
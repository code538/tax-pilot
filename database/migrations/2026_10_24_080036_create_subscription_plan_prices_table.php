<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plan_prices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subscription_plan_id')
                ->constrained('subscription_plans')
                ->restrictOnDelete();

            $table->string('billing_interval', 20)->default('yearly');
            $table->unsignedInteger('interval_count')->default(1);

            // GBP 59.00 = 5900 pence.
            $table->unsignedBigInteger('amount_minor');

            $table->string('currency', 3)->default('GBP');

            // Stripe identifiers are populated after Stripe setup.
            $table->string('stripe_product_id')->nullable();
            $table->string('stripe_price_id')->nullable()->unique();

            $table->boolean('is_active')->default(true);

            $table->timestamp('effective_from')->nullable();
            $table->timestamp('effective_until')->nullable();

            $table->timestamps();

            $table->index([
                'subscription_plan_id',
                'is_active',
            ], 'subscription_price_plan_active_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plan_prices');
    }
};
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
            // for example, "recurring" or "one_time" payment modes.    
            $table->string('payment_mode', 20)->default('recurring');
            $table->string('paypal_plan_id')->nullable();
            $table->string('billing_interval', 20)->nullable()
                ->comment('For recurring payments, e.g., daily, weekly, monthly, quarterly, yearly');
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
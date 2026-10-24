<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organisation_subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organisation_id')
                ->constrained('organisations')
                ->restrictOnDelete();

            $table->foreignId('subscription_plan_id')
                ->constrained('subscription_plans')
                ->restrictOnDelete();

            $table->foreignId('subscription_plan_price_id')
                ->nullable()
                ->constrained('subscription_plan_prices')
                ->restrictOnDelete();

            $table->string('status', 30)->default('pending');

            $table->timestamp('starts_at')->nullable();

            $table->timestamp('current_period_starts_at')->nullable();
            $table->timestamp('current_period_ends_at')->nullable();

            $table->timestamp('ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->string('provider', 30)->nullable();

            $table->string('provider_customer_id')->nullable();
            $table->string('provider_subscription_id')
                ->nullable()
                ->unique();

            // Snapshot of the commercial terms at purchase time.
            $table->unsignedBigInteger('amount_minor')->nullable();
            $table->string('currency', 3)->default('GBP');
            $table->string('billing_interval', 20)->default('yearly');

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index([
                'organisation_id',
                'status',
            ], 'org_subscription_status_idx');

            $table->index([
                'status',
                'current_period_ends_at',
            ], 'subscription_expiry_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organisation_subscriptions');
    }
};
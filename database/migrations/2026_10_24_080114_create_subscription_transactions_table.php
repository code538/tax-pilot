<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organisation_subscription_id')
                ->constrained('organisation_subscriptions')
                ->restrictOnDelete();

            $table->string('provider', 30);

            $table->string('provider_transaction_id')
                ->nullable()
                ->unique();

            $table->unsignedBigInteger('amount_minor');
            $table->string('currency', 3)->default('GBP');

            $table->string('status', 30)->default('pending');
            $table->string('transaction_type', 30)->default('payment');

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index([
                'organisation_subscription_id',
                'status',
            ], 'subscription_transaction_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_transactions');
    }
};
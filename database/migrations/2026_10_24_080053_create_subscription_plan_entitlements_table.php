<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plan_entitlements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subscription_plan_id')
                ->constrained('subscription_plans')
                ->cascadeOnDelete();

            $table->string('entitlement_key', 100);
            $table->string('value_type', 20);

            $table->json('value');

            $table->timestamps();

            $table->unique([
                'subscription_plan_id',
                'entitlement_key',
            ], 'plan_entitlement_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plan_entitlements');
    }
};
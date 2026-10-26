<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->string('tagline')->nullable();
            $table->string('badge_text')->nullable();
            $table->string('button_text')->default('Get Started Free');
            $table->string('display_group', 50)->default('main');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn([
                'tagline',
                'badge_text',
                'button_text',
                'display_group',
            ]);
        });
    }
};
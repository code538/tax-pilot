<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_first_section', function (Blueprint $table) {
            $table->id();
            $table->string('page_title');
            $table->string('slug')->index();
            $table->string('batch')->nullable();
            $table->string('heading');
            $table->string('heading_meta')->nullable();
            $table->text('description')->nullable();
            $table->string('desc_meta')->nullable();
            $table->string('primary_button')->nullable();
            $table->string('primary_button_url')->nullable();
            $table->string('secondary_button')->nullable();
            $table->string('secondary_button_url')->nullable();
            $table->string('trust_text')->nullable();
            $table->string('web_image')->nullable();
            $table->string('mobile_image')->nullable();
            $table->string('image_alt')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_first_section');
    }
};

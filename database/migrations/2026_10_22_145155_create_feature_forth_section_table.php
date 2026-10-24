<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_forth_section', function (Blueprint $table) {
            $table->id();

            $table->string('page_name');
            $table->string('slug')->index();

            $table->string('title');
            $table->text('description')->nullable();

            $table->json('feature')->nullable();

            $table->string('mobile_image')->nullable();
            $table->string('web_image')->nullable();
            $table->string('image_alt')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_forth_section');
    }
};
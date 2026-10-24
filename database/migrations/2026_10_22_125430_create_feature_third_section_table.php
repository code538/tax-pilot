<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_third_section', function (Blueprint $table) {
            $table->id();

            $table->string('page_title');
            $table->string('slug')->index();

            $table->string('section_heading')->nullable();
            $table->text('section_desc')->nullable();

            $table->string('icon')->nullable();
            $table->string('title');
            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_third_section');
    }
};
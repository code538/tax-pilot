<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_fifth_section', function (Blueprint $table) {
            $table->id();

            $table->string('page_name');
            $table->string('slug')->index();

            $table->string('title');
            $table->text('description')->nullable();

            $table->json('feature')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_fifth_section');
    }
};
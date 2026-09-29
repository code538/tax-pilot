<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filing_types', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150);

            $table->string('slug', 100)
                ->unique('filing_type_slug_unq');

            /*
             * Tax / Companies House / Accounts / etc.
             */
            $table->string('category', 50);

            /*
             * External authority responsible for filing.
             *
             * hmrc
             * companies_house
             * internal
             */
            $table->string('authority', 50)->nullable();

            $table->text('description')->nullable();

            /*
             * Whether users can currently create
             * this type of filing.
             */
            $table->boolean('is_active')->default(true);

            /*
             * Controls display/order in UI.
             */
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(
                ['category', 'is_active'],
                'filing_type_cat_active_idx'
            );

            $table->index(
                ['authority', 'is_active'],
                'filing_type_auth_active_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filing_types');
    }
};
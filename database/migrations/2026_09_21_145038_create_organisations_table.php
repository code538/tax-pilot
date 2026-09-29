<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organisations', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();

            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();

            $table->string('website')->nullable();

            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('county')->nullable();
            $table->string('postcode', 20)->nullable();
            $table->string('country', 100)->default('United Kingdom');

            /*
             * Organisation type:
             * accountant
             * tax_agent
             * company
             * individual
             * other
             */
            $table->string('type', 50)->default('tax_agent');

            /*
             * active / inactive / suspended
             */
            $table->string('status', 30)->default('active');

            $table->string('timezone', 100)->default('Europe/London');

            $table->string('currency', 10)->default('GBP');

            $table->string('logo')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status', 'org_status_idx');
            $table->index('type', 'org_type_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organisations');
    }
};
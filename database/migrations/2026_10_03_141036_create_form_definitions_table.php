
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_definitions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('filing_type_id')
                ->constrained('filing_types')
                ->restrictOnDelete();

            $table->string('name', 150);
            $table->string('slug', 100)->unique('form_def_slug_unq');

            $table->string('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(
                ['filing_type_id', 'is_active'],
                'form_def_type_active_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_definitions');
    }
};
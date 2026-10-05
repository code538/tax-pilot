<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('filing_versions', function (Blueprint $table) {
            $table->foreignId('form_version_id')
                ->nullable()
                ->after('organisation_id')
                ->constrained('form_versions')
                ->restrictOnDelete();

            $table->index(
                ['form_version_id', 'filing_id'],
                'fil_ver_form_filing_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('filing_versions', function (Blueprint $table) {
            $table->dropForeign(['form_version_id']);

            $table->dropIndex('fil_ver_form_filing_idx');

            $table->dropColumn('form_version_id');
        });
    }
};
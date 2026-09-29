<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filing_subjects', function (Blueprint $table) {
            $table->id();

            /*
             * Organisation that owns/manages this filing subject.
             *
             * Example:
             * Organisation 1 manages its own filings
             * and filings for client companies.
             */
            $table->foreignId('organisation_id')
                ->constrained('organisations')
                ->cascadeOnDelete();

            /*
             * Who is the filing actually for?
             *
             * organisation = the organisation itself
             * company      = another company/client
             */
            $table->string('subject_type', 30);

            /*
             * Legal/business name.
             *
             * For organisation:
             * ABC Tax & Accountants
             *
             * For company:
             * XYZ Limited
             */
            $table->string('name');

            /*
             * Companies House information
             */
            $table->string('company_number', 50)->nullable();
            $table->string('company_type', 100)->nullable();

            /*
             * Tax identifiers
             */
            $table->string('utr_number', 50)->nullable();
            $table->string('vat_number', 50)->nullable();

            /*
             * Contact information
             */
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();

            /*
             * Registered/business address
             */
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('county')->nullable();
            $table->string('postcode', 20)->nullable();
            $table->string('country', 100)->default('United Kingdom');

            /*
             * Companies House status
             */
            $table->string('companies_house_status', 100)->nullable();

            $table->date('incorporation_date')->nullable();
            $table->date('dissolution_date')->nullable();

            /*
             * Active/inactive filing subject
             */
            $table->string('status', 30)->default('active');

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            /*
             * Short indexes/constraints.
             */
            $table->index(
                ['organisation_id', 'subject_type'],
                'fs_org_type_idx'
            );

            $table->index(
                ['organisation_id', 'status'],
                'fs_org_status_idx'
            );

            $table->index(
                ['organisation_id', 'name'],
                'fs_org_name_idx'
            );

            $table->index(
                ['organisation_id', 'company_number'],
                'fs_org_co_num_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filing_subjects');
    }
};
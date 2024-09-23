<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('production_request', function (Blueprint $table) {
            $table->integer('pe_id', true);
            $table->unsignedInteger('pe_num')->index('pe_num');
            $table->integer('pe_requested_by');
            $table->dateTime('pe_date_request');
            $table->date('pe_date_needed');
            $table->string('pe_file_docno', 40);
            $table->text('pe_remarks');
            $table->integer('pe_generate_code')->default(0);
            $table->integer('pe_requisition')->default(0);
            $table->integer('pe_status')->default(0);
            $table->integer('pe_type');
            $table->integer('pe_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_request');
    }
};

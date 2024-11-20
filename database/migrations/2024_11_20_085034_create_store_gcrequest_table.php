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
        Schema::create('store_gcrequest', function (Blueprint $table) {
            $table->integer('sgc_id', true);
            $table->unsignedInteger('sgc_num');
            $table->integer('sgc_requested_by');
            $table->dateTime('sgc_date_request');
            $table->date('sgc_date_needed')->nullable();
            $table->string('sgc_file_docno', 50);
            $table->text('sgc_remarks');
            $table->integer('sgc_status');
            $table->integer('sgc_store')->index('sgc_store');
            $table->string('sgc_rec', 2)->default('');
            $table->string('sgc_cancel', 3)->default('');
            $table->string('sgc_type', 20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_gcrequest');
    }
};

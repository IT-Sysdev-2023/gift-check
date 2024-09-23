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
        Schema::create('approved_production_request', function (Blueprint $table) {
            $table->integer('ape_id', true);
            $table->integer('ape_pro_request_id');
            $table->string('ape_approved_by', 60);
            $table->string('ape_checked_by', 60);
            $table->integer('ape_preparedby');
            $table->text('ape_remarks');
            $table->dateTime('ape_approved_at');
            $table->string('ape_file_doc_no', 30);
            $table->unsignedInteger('ape_ledgernum');
            $table->string('ape_received', 3)->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approved_production_request');
    }
};

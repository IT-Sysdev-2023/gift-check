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
        Schema::create('budget_request', function (Blueprint $table) {
            $table->integer('br_id', true);
            $table->decimal('br_request', 10);
            $table->unsignedInteger('br_no')->index('br_no');
            $table->integer('br_requested_by');
            $table->integer('br_checked_by')->nullable();
            $table->dateTime('br_requested_at');
            $table->date('br_requested_needed')->nullable();
            $table->string('br_file_docno', 30);
            $table->text('br_remarks');
            $table->integer('br_request_status');
            $table->integer('br_type');
            $table->integer('br_group');
            $table->integer('br_preapprovedby')->nullable();
            $table->enum('br_category', ['regular', 'special'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_request');
    }
};

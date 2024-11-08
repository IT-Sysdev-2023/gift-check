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
        Schema::create('approved_budget_request', function (Blueprint $table) {
            $table->integer('abr_id', true);
            $table->integer('abr_budget_request_id');
            $table->string('abr_approved_by', 50);
            $table->string('abr_checked_by', 60);
            $table->dateTime('abr_approved_at');
            $table->string('abr_prepared_by', 50);
            $table->text('approved_budget_remark');
            $table->string('abr_file_doc_no', 30);
            $table->unsignedInteger('abr_ledgerefnum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approved_budget_request');
    }
};

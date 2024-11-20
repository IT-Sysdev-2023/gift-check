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
        Schema::create('approved_adjustment_request', function (Blueprint $table) {
            $table->integer('app_adjid', true);
            $table->integer('app_adj_request_id');
            $table->string('app_approved_by', 50);
            $table->string('app_checked_by', 50);
            $table->dateTime('app_approved_at');
            $table->string('app_prepared_by', 50);
            $table->text('app_adj_remark');
            $table->string('app_file_doc_no', 50);
            $table->integer('app_ledgerefnum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approved_adjustment_request');
    }
};

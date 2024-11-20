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
        Schema::create('budgetadjustment', function (Blueprint $table) {
            $table->integer('adj_id', true);
            $table->decimal('adj_request', 10);
            $table->integer('adj_no')->unique('adj_no');
            $table->integer('adj_requested_by');
            $table->dateTime('adj_requested_at');
            $table->string('adj_file_docno', 30);
            $table->string('adjust_type', 10);
            $table->text('adj_remarks');
            $table->string('adj_request_status', 11);
            $table->integer('adj_type');
            $table->integer('adj_group')->nullable();
            $table->string('adj_preapprovedby', 30)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgetadjustment');
    }
};

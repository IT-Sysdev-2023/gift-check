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
        Schema::create('custodian_srr', function (Blueprint $table) {
            $table->integer('csrr_id')->primary();
            $table->integer('csrr_requisition')->index('csrr_requisition');
            $table->string('csrr_receivetype', 10);
            $table->string('csrr_receivedas', 15);
            $table->string('csrr_remarks', 100)->default('');
            $table->dateTime('csrr_datetime');
            $table->string('csrr_checked_by', 50)->default('');
            $table->integer('csrr_prepared_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custodian_srr');
    }
};

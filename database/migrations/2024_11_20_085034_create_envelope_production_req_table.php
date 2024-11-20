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
        Schema::create('envelope_production_req', function (Blueprint $table) {
            $table->integer('env_pe_id', true);
            $table->unsignedInteger('env_num');
            $table->integer('env_req_by');
            $table->dateTime('env_date_req');
            $table->date('env_date_needed');
            $table->string('env_remarks', 100);
            $table->integer('env_generate');
            $table->integer('env_requisition');
            $table->integer('env_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envelope_production_req');
    }
};

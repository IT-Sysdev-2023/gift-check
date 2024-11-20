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
        Schema::create('cancelled_budget_request', function (Blueprint $table) {
            $table->integer('cdreq_id', true);
            $table->integer('cdreq_req_id');
            $table->dateTime('cdreq_at');
            $table->integer('cdreq_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancelled_budget_request');
    }
};

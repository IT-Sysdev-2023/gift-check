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
        Schema::create('cancelled_adj_request', function (Blueprint $table) {
            $table->integer('cadj_id', true);
            $table->integer('cadj_req_id');
            $table->dateTime('cadj_at');
            $table->integer('cadj_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancelled_adj_request');
    }
};

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
        Schema::create('gc_remaining_request', function (Blueprint $table) {
            $table->integer('gc_remainreq_id', true);
            $table->integer('gc_remainreq_den')->index('gc_remainreq_den');
            $table->integer('gc_remainreq_store')->index('gc_remainreq_store');
            $table->dateTime('gc_remainreq_date');
            $table->integer('gc_remainreq_reqno');
            $table->integer('gc_remainreq_rel_no');
            $table->integer('gc_remainreq_stat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gc_remaining_request');
    }
};

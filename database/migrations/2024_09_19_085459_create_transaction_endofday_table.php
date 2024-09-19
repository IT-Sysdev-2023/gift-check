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
        Schema::create('transaction_endofday', function (Blueprint $table) {
            $table->integer('eod_id', true);
            $table->integer('eod_store')->index('eod_store');
            $table->integer('eod_supervisor_id');
            $table->integer('eod_transtart');
            $table->integer('eod_transend');
            $table->dateTime('eod_datetime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_endofday');
    }
};

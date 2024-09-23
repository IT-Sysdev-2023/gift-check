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
        Schema::create('temp_receivestore', function (Blueprint $table) {
            $table->bigInteger('trec_barcode')->primary();
            $table->integer('trec_recnum');
            $table->integer('trec_store')->index('trec_store');
            $table->integer('trec_denid')->index('trec_denid');
            $table->integer('trec_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_receivestore');
    }
};

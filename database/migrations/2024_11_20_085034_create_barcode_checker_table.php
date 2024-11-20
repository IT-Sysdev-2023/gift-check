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
        Schema::create('barcode_checker', function (Blueprint $table) {
            $table->bigInteger('bcheck_barcode')->primary();
            $table->integer('bcheck_checkby')->index('bcheck_checkby');
            $table->dateTime('bcheck_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barcode_checker');
    }
};

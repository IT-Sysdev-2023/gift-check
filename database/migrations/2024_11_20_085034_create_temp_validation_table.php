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
        Schema::create('temp_validation', function (Blueprint $table) {
            $table->bigInteger('tval_barcode')->primary();
            $table->integer('tval_recnum');
            $table->integer('tval_denom')->index('tval_denom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_validation');
    }
};

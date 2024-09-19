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
        Schema::create('temp_sales', function (Blueprint $table) {
            $table->integer('ts_id', true);
            $table->bigInteger('ts_barcode_no')->index('ts_barcode_no');
            $table->date('ts_date');
            $table->time('ts_time');
            $table->integer('ts_cashier_id')->index('ts_cashier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_sales');
    }
};

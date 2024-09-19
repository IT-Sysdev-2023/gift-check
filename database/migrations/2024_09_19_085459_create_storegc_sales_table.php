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
        Schema::create('storegc_sales', function (Blueprint $table) {
            $table->integer('sgc_id', true);
            $table->bigInteger('sgc_barcode_no')->index('sgc_barcode_no');
            $table->integer('sgc_cashier_code');
            $table->date('sgc_date');
            $table->time('sgc_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storegc_sales');
    }
};

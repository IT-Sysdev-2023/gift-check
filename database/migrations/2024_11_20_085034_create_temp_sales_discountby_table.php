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
        Schema::create('temp_sales_discountby', function (Blueprint $table) {
            $table->bigInteger('tsd_barcode')->primary();
            $table->integer('tsd_disc_type');
            $table->decimal('tsd_disc_percent', 12, 5);
            $table->decimal('tsd_disc_amt', 12);
            $table->integer('tsd_cashier');
            $table->integer('tsd_discountby')->index('tsd_discountby');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_sales_discountby');
    }
};

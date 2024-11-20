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
        Schema::create('store_void_items', function (Blueprint $table) {
            $table->integer('svi_id', true);
            $table->bigInteger('svi_barcodes')->index('svi_barcodes');
            $table->integer('svi_transaction')->default(0);
            $table->integer('svi_store')->index('svi_store');
            $table->integer('svi_denom')->index('svi_denom');
            $table->integer('svi_cashier');
            $table->integer('svi_supervisor')->default(0);
            $table->dateTime('svi_datetime');
            $table->integer('svi_eod')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_void_items');
    }
};

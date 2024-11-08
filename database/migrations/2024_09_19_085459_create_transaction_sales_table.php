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
        Schema::create('transaction_sales', function (Blueprint $table) {
            $table->integer('sales_id', true);
            $table->integer('sales_transaction_id')->index('sales_transaction_id');
            $table->bigInteger('sales_barcode')->index('sales_barcode');
            $table->integer('sales_denomination')->index('sales_denomination');
            $table->integer('sales_gc_type');
            $table->integer('sales_item_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_sales');
    }
};

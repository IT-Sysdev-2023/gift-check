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
        Schema::create('gc_return', function (Blueprint $table) {
            $table->integer('rr_id', true);
            $table->bigInteger('rr_barcode_no')->index('rr_barcode_no');
            $table->unsignedInteger('rr_transaction_num');
            $table->dateTime('rr_datetime');
            $table->integer('rr_store')->index('rr_store');
            $table->integer('rr_cashier')->index('rr_cashier');
            $table->integer('rr_supervisor');
            $table->integer('rr_denom_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gc_return');
    }
};

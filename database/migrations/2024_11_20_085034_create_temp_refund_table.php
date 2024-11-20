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
        Schema::create('temp_refund', function (Blueprint $table) {
            $table->integer('trfund_id', true);
            $table->bigInteger('trfund_barcode')->index('trfund_barcode');
            $table->decimal('trfund_linedisc', 10);
            $table->decimal('trfund_subdisc', 10);
            $table->integer('trfund_store')->index('trfund_store');
            $table->integer('trfund_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_refund');
    }
};

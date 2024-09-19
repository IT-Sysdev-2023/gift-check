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
        Schema::create('store_eod_items', function (Blueprint $table) {
            $table->integer('st_eod_id', true);
            $table->bigInteger('st_eod_barcode')->index('st_eod_barcode');
            $table->integer('st_eod_trid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_eod_items');
    }
};

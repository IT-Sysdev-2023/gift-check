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
        Schema::create('institut_transactions_items', function (Blueprint $table) {
            $table->integer('instituttritems_id', true);
            $table->bigInteger('instituttritems_barcode')->index('instituttritems_barcode');
            $table->integer('instituttritems_trid')->index('instituttritems_trid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institut_transactions_items');
    }
};

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
        Schema::create('gc_adjustment_items', function (Blueprint $table) {
            $table->integer('gc_adji_id', true);
            $table->integer('gc_adji_ids');
            $table->integer('gc_adji_den');
            $table->bigInteger('gc_adji_barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gc_adjustment_items');
    }
};

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
        Schema::create('allocation_adjustment_items', function (Blueprint $table) {
            $table->integer('aadji_id', true);
            $table->integer('aadji_aadj_id');
            $table->bigInteger('aadji_barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allocation_adjustment_items');
    }
};

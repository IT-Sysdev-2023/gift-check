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
        Schema::create('production_adjustment_items', function (Blueprint $table) {
            $table->integer('proadji_id', true);
            $table->integer('proadji_proadj_id');
            $table->bigInteger('proadji_barcode')->index('proadji_barcode');
            $table->integer('proadji_denom')->index('proadji_denom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_adjustment_items');
    }
};

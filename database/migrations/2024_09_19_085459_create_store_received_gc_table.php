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
        Schema::create('store_received_gc', function (Blueprint $table) {
            $table->integer('strec_id', true);
            $table->bigInteger('strec_barcode')->index('strec_barcode');
            $table->integer('strec_storeid')->index('strec_storeid');
            $table->integer('strec_recnum');
            $table->integer('strec_denom');
            $table->string('strec_sold', 3)->default('');
            $table->string('strec_return', 3)->default('');
            $table->string('strec_transfer_out', 3)->default('');
            $table->string('strec_bng_tag', 5)->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_received_gc');
    }
};

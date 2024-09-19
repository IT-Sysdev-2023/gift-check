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
        Schema::create('lost_gc_barcodes', function (Blueprint $table) {
            $table->integer('lostgcb_id', true);
            $table->bigInteger('lostgcb_barcode')->index('lostgcb_barcode');
            $table->decimal('lostgcb_denom', 10)->index('lostgcb_denom');
            $table->integer('lostgcb_repid');
            $table->string('lostgcb_status', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lost_gc_barcodes');
    }
};

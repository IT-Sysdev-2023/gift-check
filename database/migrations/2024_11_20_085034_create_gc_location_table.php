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
        Schema::create('gc_location', function (Blueprint $table) {
            $table->integer('loc_id', true);
            $table->bigInteger('loc_barcode_no')->index('loc_barcode_no');
            $table->integer('loc_store_id')->index('loc_store_id');
            $table->date('loc_date');
            $table->time('loc_time');
            $table->integer('loc_gc_type');
            $table->string('loc_rel', 3);
            $table->integer('loc_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gc_location');
    }
};

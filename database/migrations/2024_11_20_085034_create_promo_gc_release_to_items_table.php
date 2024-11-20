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
        Schema::create('promo_gc_release_to_items', function (Blueprint $table) {
            $table->integer('prreltoi_id', true);
            $table->bigInteger('prreltoi_barcode')->index('prreltoi_barcode');
            $table->integer('prreltoi_relid')->index('prreltoi_relid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_gc_release_to_items');
    }
};

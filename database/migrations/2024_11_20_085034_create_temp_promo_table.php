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
        Schema::create('temp_promo', function (Blueprint $table) {
            $table->bigInteger('tp_barcode')->index('tp_barcode');
            $table->integer('tp_den')->index('tp_den');
            $table->integer('tp_promoid')->index('tp_promoid');
            $table->integer('tp_gctype');
            $table->integer('tp_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_promo');
    }
};

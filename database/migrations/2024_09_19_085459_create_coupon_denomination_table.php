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
        Schema::create('coupon_denomination', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('coup_barcode_start', 50)->default('0');
            $table->string('coup_status', 50);
            $table->integer('coup_cby');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_denomination');
    }
};

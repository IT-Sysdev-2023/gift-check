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
        Schema::create('promo_gc', function (Blueprint $table) {
            $table->integer('prom_promoid');
            $table->bigInteger('prom_barcode')->primary();
            $table->integer('prom_denom');
            $table->integer('prom_gctype');
            $table->integer('pr_stat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_gc');
    }
};
